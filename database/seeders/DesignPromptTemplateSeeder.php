<?php

namespace Database\Seeders;

use App\Models\DesignPromptTemplate;
use Illuminate\Database\Seeder;

class DesignPromptTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Poster Kegiatan Kajian',
                'module_type' => 'kegiatan',
                'design_type' => 'poster',
                'canvas_size' => '1080x1080',
                'platforms' => ['Instagram', 'Facebook', 'WhatsApp', 'Website'],
                'tone' => 'Islami modern, resmi, informatif',
                'style' => 'Poster kajian mudah dibaca di HP',
                'color_palette' => 'Hijau tua, putih, emas lembut',
                'target_audience' => 'Jamaah masjid',
                'layout_density' => 'Rapi seimbang',
                'elements' => ['Logo masjid', 'Ikon kalender', 'Ikon lokasi', 'Ornamen Islami secukupnya'],
                'required_text_rules' => ['Nama kegiatan paling besar', 'Tema materi menonjol', 'Tanggal waktu lokasi jelas'],
                'photo_rules' => ['Jika memakai foto narasumber, pertahankan wajah asli dan identitas orang'],
                'prompt_structure' => "Buatkan desain flyer acara Islami untuk kegiatan masjid.\n\nUkuran desain: 1080 x 1080 px, rasio 1:1, cocok untuk Instagram, Facebook, WhatsApp, dan website.\n\nInformasi kegiatan:\n- Nama kegiatan: {judul}\n- Jenis kegiatan: {jenis_kegiatan}\n- Tema materi: {tema_materi}\n- Pemateri/Narasumber: {narasumber}\n- Tanggal: {tanggal}\n- Waktu: {jam}\n- Lokasi: {lokasi}\n- Masjid/Penyelenggara: {nama_masjid}\n- Deskripsi singkat: {deskripsi}\n\nArahan: buat flyer resmi, informatif, mudah dibaca di HP, dengan hierarki teks jelas. Jangan menambahkan informasi yang tidak ada.",
            ],
            [
                'name' => 'Flyer Program Donasi',
                'module_type' => 'donasi',
                'design_type' => 'flyer',
                'canvas_size' => '1080x1080',
                'platforms' => ['Instagram', 'Facebook', 'WhatsApp', 'Website'],
                'tone' => 'Amanah, hangat, mengajak kebaikan',
                'style' => 'Flyer donasi modern dan jelas',
                'color_palette' => 'Hijau tua, putih, emas lembut',
                'target_audience' => 'Jamaah dan donatur',
                'layout_density' => 'Normal',
                'elements' => ['Progress donasi', 'Ikon sedekah', 'Area rekening', 'Area QRIS jika tersedia'],
                'required_text_rules' => ['Judul program jelas', 'Target dan terkumpul mudah dibaca', 'Rekening terlihat jelas'],
                'photo_rules' => [],
                'prompt_structure' => "Buatkan desain flyer program donasi masjid.\n\nUkuran desain: 1080 x 1080 px, rasio 1:1, cocok untuk Instagram, Facebook, WhatsApp, dan website.\n\nInformasi donasi:\n- Judul program: {judul}\n- Kategori: {category}\n- Target dana: {target_amount}\n- Dana terkumpul: {collected_amount}\n- Rekening: {rekening}\n- QRIS: {qris}\n- Masjid/Penyelenggara: {nama_masjid}\n- Deskripsi: {deskripsi}\n\nArahan: desain harus membangun kepercayaan, informatif, tidak berlebihan, dan menampilkan instruksi donasi dengan jelas. Jangan membuat nomor rekening atau nominal palsu.",
            ],
            [
                'name' => 'Pengumuman Masjid',
                'module_type' => 'pengumuman',
                'design_type' => 'banner',
                'canvas_size' => '1080x1080',
                'platforms' => ['Instagram', 'WhatsApp', 'Website'],
                'tone' => 'Resmi dan jelas',
                'style' => 'Pengumuman publik masjid',
                'color_palette' => 'Hijau tua, putih, aksen emas',
                'target_audience' => 'Jamaah masjid',
                'layout_density' => 'Sangat ringkas',
                'elements' => ['Logo masjid', 'Panel judul', 'Ikon informasi'],
                'required_text_rules' => ['Judul pengumuman jelas', 'Isi ringkas mudah dibaca'],
                'photo_rules' => [],
                'prompt_structure' => "Buatkan desain pengumuman resmi masjid.\n\nJudul: {judul}\nDeskripsi: {deskripsi}\nMasjid: {nama_masjid}\n\nDesain harus jelas, resmi, kontras tinggi, dan mudah dibaca di HP.",
            ],
            [
                'name' => 'Berita atau Artikel Islami',
                'module_type' => 'umum',
                'design_type' => 'thumbnail',
                'canvas_size' => '1080x1080',
                'platforms' => ['Website', 'Instagram', 'Facebook'],
                'tone' => 'Editorial Islami modern',
                'style' => 'Thumbnail konten publik',
                'color_palette' => 'Hijau tua, putih, warna pendukung lembut',
                'target_audience' => 'Pembaca website masjid',
                'layout_density' => 'Rapi seimbang',
                'elements' => ['Judul besar', 'Ilustrasi relevan', 'Identitas masjid'],
                'required_text_rules' => ['Judul paling jelas', 'Tidak terlalu banyak teks'],
                'photo_rules' => [],
                'prompt_structure' => "Buatkan desain thumbnail konten website masjid.\n\nJudul: {judul}\nDeskripsi: {deskripsi}\nMasjid: {nama_masjid}\n\nDesain harus modern, Islami, editorial, dan mudah dipahami.",
            ],
        ];

        foreach ($templates as $template) {
            DesignPromptTemplate::updateOrCreate(
                ['mosque_id' => null, 'name' => $template['name']],
                array_merge($template, [
                    'is_active' => true,
                    'created_by' => null,
                    'updated_by' => null,
                ])
            );
        }
    }
}
