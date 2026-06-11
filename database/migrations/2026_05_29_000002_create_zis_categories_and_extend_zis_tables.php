<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_CATEGORIES = [
        ['name' => 'Zakat Fitrah', 'type' => 'zakat'],
        ['name' => 'Zakat Maal', 'type' => 'zakat'],
        ['name' => "Infak Jum'at", 'type' => 'infak'],
        ['name' => 'Infak Subuh', 'type' => 'infak'],
        ['name' => 'Sedekah Jamaah', 'type' => 'sedekah'],
        ['name' => 'Sedekah Anak Yatim', 'type' => 'sedekah'],
    ];

    public function up(): void
    {
        Schema::create('zis_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->nullable()->constrained('mosques')->nullOnDelete();
            $table->string('name');
            $table->string('type');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('zis_receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_receipts', 'zis_category_id')) {
                $table->foreignId('zis_category_id')->nullable()->after('id')->constrained('zis_categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('zis_receipts', 'receipt_date')) {
                $table->date('receipt_date')->nullable()->after('zis_category_id');
            }

            if (! Schema::hasColumn('zis_receipts', 'donor_name')) {
                $table->string('donor_name')->nullable()->after('receipt_date');
            }

            if (! Schema::hasColumn('zis_receipts', 'donor_phone')) {
                $table->string('donor_phone')->nullable()->after('donor_name');
            }

            if (! Schema::hasColumn('zis_receipts', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable()->after('donor_phone');
            }

            if (! Schema::hasColumn('zis_receipts', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }

            if (! Schema::hasColumn('zis_receipts', 'description')) {
                $table->text('description')->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('zis_receipts', 'created_by')) {
                $table->string('created_by')->nullable()->after('description');
            }
        });

        Schema::table('zis_distributions', function (Blueprint $table) {
            if (! Schema::hasColumn('zis_distributions', 'zis_category_id')) {
                $table->foreignId('zis_category_id')->nullable()->after('id')->constrained('zis_categories')->nullOnDelete();
            }

            if (! Schema::hasColumn('zis_distributions', 'distribution_date')) {
                $table->date('distribution_date')->nullable()->after('zis_category_id');
            }

            if (! Schema::hasColumn('zis_distributions', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('distribution_date');
            }

            if (! Schema::hasColumn('zis_distributions', 'recipient_phone')) {
                $table->string('recipient_phone')->nullable()->after('recipient_name');
            }

            if (! Schema::hasColumn('zis_distributions', 'recipient_address')) {
                $table->text('recipient_address')->nullable()->after('recipient_phone');
            }

            if (! Schema::hasColumn('zis_distributions', 'recipient_type')) {
                $table->string('recipient_type')->nullable()->after('recipient_address');
            }

            if (! Schema::hasColumn('zis_distributions', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable()->after('recipient_type');
            }

            if (! Schema::hasColumn('zis_distributions', 'description')) {
                $table->text('description')->nullable()->after('amount');
            }

            if (! Schema::hasColumn('zis_distributions', 'proof_file')) {
                $table->string('proof_file')->nullable()->after('description');
            }

            if (! Schema::hasColumn('zis_distributions', 'created_by')) {
                $table->string('created_by')->nullable()->after('proof_file');
            }
        });

        $this->seedDefaultCategories();
        $this->backfillReceipts();
        $this->backfillDistributions();
    }

    public function down(): void
    {
        Schema::table('zis_distributions', function (Blueprint $table) {
            foreach (['created_by', 'proof_file', 'description', 'amount', 'recipient_type', 'recipient_address', 'recipient_phone', 'recipient_name', 'distribution_date'] as $column) {
                if (Schema::hasColumn('zis_distributions', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('zis_distributions', 'zis_category_id')) {
                $table->dropConstrainedForeignId('zis_category_id');
            }
        });

        Schema::table('zis_receipts', function (Blueprint $table) {
            foreach (['created_by', 'description', 'payment_method', 'amount', 'donor_phone', 'donor_name', 'receipt_date'] as $column) {
                if (Schema::hasColumn('zis_receipts', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('zis_receipts', 'zis_category_id')) {
                $table->dropConstrainedForeignId('zis_category_id');
            }
        });

        Schema::dropIfExists('zis_categories');
    }

    private function seedDefaultCategories(): void
    {
        $now = now();
        $mosqueIds = DB::table('mosques')->pluck('id');

        foreach ($mosqueIds as $mosqueId) {
            foreach (self::DEFAULT_CATEGORIES as $category) {
                DB::table('zis_categories')->updateOrInsert(
                    ['mosque_id' => $mosqueId, 'name' => $category['name']],
                    [
                        'type' => $category['type'],
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    private function backfillReceipts(): void
    {
        DB::table('zis_receipts')
            ->leftJoin('muzakkis', 'zis_receipts.muzakki_id', '=', 'muzakkis.id')
            ->update([
                'zis_receipts.receipt_date' => DB::raw('zis_receipts.tanggal'),
                'zis_receipts.donor_name' => DB::raw('muzakkis.nama'),
                'zis_receipts.donor_phone' => DB::raw('muzakkis.no_hp'),
                'zis_receipts.amount' => DB::raw('zis_receipts.nominal_uang'),
                'zis_receipts.payment_method' => DB::raw('zis_receipts.metode_pembayaran'),
                'zis_receipts.description' => DB::raw('zis_receipts.keterangan'),
                'zis_receipts.created_by' => DB::raw('zis_receipts.diterima_oleh'),
            ]);
    }

    private function backfillDistributions(): void
    {
        DB::table('zis_distributions')
            ->leftJoin('mustahiks', 'zis_distributions.mustahik_id', '=', 'mustahiks.id')
            ->update([
                'zis_distributions.distribution_date' => DB::raw('zis_distributions.tanggal'),
                'zis_distributions.recipient_name' => DB::raw('mustahiks.nama'),
                'zis_distributions.recipient_phone' => DB::raw('mustahiks.no_hp'),
                'zis_distributions.recipient_address' => DB::raw('mustahiks.alamat'),
                'zis_distributions.recipient_type' => DB::raw('LOWER(REPLACE(zis_distributions.kategori_asnaf, " ", "_"))'),
                'zis_distributions.amount' => DB::raw('zis_distributions.nominal'),
                'zis_distributions.description' => DB::raw('zis_distributions.keterangan'),
                'zis_distributions.proof_file' => DB::raw('zis_distributions.bukti_serah_terima'),
                'zis_distributions.created_by' => DB::raw('zis_distributions.disalurkan_oleh'),
            ]);
    }
};
