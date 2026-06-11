<?php

namespace Tests\Feature;

use App\Models\Jamaah;
use App\Models\JamaahCategory;
use App\Models\Mosque;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JamaahCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_entry_can_store_multiple_categories_and_fallback_category(): void
    {
        [$user, $mosque] = $this->operatorWithMosque();
        $active = JamaahCategory::where('name', 'jamaah_aktif')->firstOrFail();
        $donor = JamaahCategory::where('name', 'donatur')->firstOrFail();

        $response = $this->actingAs($user)
            ->withSession(['active_mosque_id' => $mosque->id])
            ->post(route('jamaah.store'), $this->jamaahData([
                'category_ids' => [$active->id, $donor->id],
            ]));

        $response->assertRedirect(route('jamaah.index'));

        $jamaah = Jamaah::where('nama', 'Jamaah Multi')->firstOrFail();
        $this->assertSame('jamaah_aktif', $jamaah->kategori);
        $this->assertSame($mosque->id, $jamaah->mosque_id);
        $this->assertEqualsCanonicalizing(
            ['jamaah_aktif', 'donatur'],
            $jamaah->categories()->pluck('name')->all()
        );
    }

    public function test_update_syncs_multiple_categories_and_index_filters_by_relation(): void
    {
        [$user, $mosque] = $this->operatorWithMosque();
        $active = JamaahCategory::where('name', 'jamaah_aktif')->firstOrFail();
        $donor = JamaahCategory::where('name', 'donatur')->firstOrFail();
        $manager = JamaahCategory::where('name', 'pengurus')->firstOrFail();
        $jamaah = new Jamaah(array_merge($this->jamaahData(), [
            'kategori' => 'jamaah_aktif',
        ]));
        $jamaah->mosque_id = $mosque->id;
        $jamaah->save();
        $jamaah->categories()->sync([$active->id]);

        $response = $this->actingAs($user)
            ->withSession(['active_mosque_id' => $mosque->id])
            ->put(route('jamaah.update', $jamaah), $this->jamaahData([
                'category_ids' => [$donor->id, $manager->id],
            ]));

        $response->assertRedirect(route('jamaah.index'));
        $this->assertSame('donatur', $jamaah->fresh()->kategori);
        $this->assertEqualsCanonicalizing(
            ['donatur', 'pengurus'],
            $jamaah->fresh()->categories()->pluck('name')->all()
        );

        $this->get(route('jamaah.index', ['kategori' => 'donatur']))
            ->assertOk()
            ->assertSeeText('Jamaah Multi');
        $this->get(route('jamaah.index', ['kategori' => 'jamaah_aktif']))
            ->assertOk()
            ->assertDontSeeText('Jamaah Multi');
    }

    public function test_manual_entry_requires_at_least_one_category(): void
    {
        [$user, $mosque] = $this->operatorWithMosque();

        $this->actingAs($user)
            ->withSession(['active_mosque_id' => $mosque->id])
            ->post(route('jamaah.store'), $this->jamaahData(['category_ids' => []]))
            ->assertSessionHasErrors('category_ids');

        $this->assertDatabaseMissing('jamaahs', ['nama' => 'Jamaah Multi']);
    }

    public function test_manual_entry_saves_custom_pekerjaan_umur_and_jamaah_tamu_category(): void
    {
        [$user, $mosque] = $this->operatorWithMosque();
        $guest = JamaahCategory::where('name', 'jamaah_tamu')->firstOrFail();

        $this->actingAs($user)
            ->withSession(['active_mosque_id' => $mosque->id])
            ->post(route('jamaah.store'), $this->jamaahData([
                'category_ids' => [$guest->id],
                'umur' => 42,
                'pekerjaan' => Jamaah::PEKERJAAN_LAINNYA,
                'pekerjaan_lainnya' => 'Teknisi AC',
            ]))
            ->assertRedirect(route('jamaah.index'));

        $jamaah = Jamaah::where('nama', 'Jamaah Multi')->firstOrFail();
        $this->assertSame('Teknisi AC', $jamaah->pekerjaan);
        $this->assertSame(42, $jamaah->umur);
        $this->assertSame('jamaah_tamu', $jamaah->kategori);

        $this->actingAs($user)
            ->withSession(['active_mosque_id' => $mosque->id])
            ->get(route('jamaah.show', $jamaah))
            ->assertOk()
            ->assertSeeText('Jamaah Tamu')
            ->assertSeeText('42 tahun')
            ->assertSeeText('Teknisi AC');
    }

    public function test_pekerjaan_lainnya_requires_description(): void
    {
        [$user, $mosque] = $this->operatorWithMosque();
        $active = JamaahCategory::where('name', 'jamaah_aktif')->firstOrFail();

        $this->actingAs($user)
            ->withSession(['active_mosque_id' => $mosque->id])
            ->post(route('jamaah.store'), $this->jamaahData([
                'category_ids' => [$active->id],
                'pekerjaan' => Jamaah::PEKERJAAN_LAINNYA,
                'pekerjaan_lainnya' => '',
            ]))
            ->assertSessionHasErrors('pekerjaan_lainnya');
    }

    private function operatorWithMosque(): array
    {
        $this->seed(RoleSeeder::class);

        $mosque = Mosque::create(['name' => 'Masjid Kategori']);
        $user = User::create([
            'name' => 'Operator',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'active_mosque_id' => $mosque->id,
        ]);
        $user->assignRole(Role::OPERATOR, $mosque->id);

        return [$user, $mosque];
    }

    private function jamaahData(array $attributes = []): array
    {
        return array_merge([
            'nama' => 'Jamaah Multi',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Masjid',
            'no_hp' => '08123456789',
            'tanggal_lahir' => null,
            'umur' => null,
            'pekerjaan' => null,
            'keahlian' => null,
            'status' => Jamaah::STATUS_VERIFIED,
            'keterangan' => null,
        ], $attributes);
    }
}
