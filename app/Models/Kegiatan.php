<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'kegiatans';

    public const JENIS_OPTIONS = [
        'Kajian Rutin',
        'Kajian Tematik',
        'Tabligh Akbar',
        'Khutbah Jumat',
        'Pengajian Anak dan Remaja',
        'Pelatihan',
        'Pesantren Kilat',
        'Program Ramadhan',
        'Peringatan Hari Besar Islam',
        'Bakti Sosial',
        'Santunan',
        'Gotong Royong',
        'Rapat Pengurus',
        'Kegiatan Pendidikan',
        'Kegiatan Kesehatan',
        'Kegiatan Sosial',
        'Lainnya',
    ];

    public const PROMPT_NUANSA_OPTIONS = [
        'Islami Modern',
        'Elegan dan Resmi',
        'Hangat dan Ramah Keluarga',
        'Semangat Anak Muda',
        'Khidmat dan Tenang',
        'Ceria dan Edukatif',
        'Sosial Kemanusiaan',
        'Ramadhan',
        'Idul Fitri',
        'Idul Adha',
    ];

    public const PROMPT_WARNA_OPTIONS = [
        'Hijau Tua, Putih, Emas',
        'Hijau Toska, Putih, Abu Terang',
        'Biru Navy, Putih, Emas',
        'Maroon, Krem, Emas',
        'Hitam, Emas, Putih',
        'Putih, Hijau, Krem',
        'Merah Putih',
        'Warna Natural Bumi',
    ];

    public const PROMPT_GAYA_OPTIONS = [
        'Modern Minimalis',
        'Formal Resmi',
        'Poster Kajian Ilmiah',
        'Flyer Dakwah Elegan',
        'Desain Remaja Kreatif',
        'Desain Ramadhan',
        'Desain Sosial Kemanusiaan',
        'Desain Masjid Klasik',
        'Desain Tipografi Kuat',
    ];

    public const PROMPT_TARGET_AUDIENS_OPTIONS = [
        'Jamaah Umum',
        'Ikhwan',
        'Akhwat',
        'Remaja',
        'Anak-anak',
        'Keluarga',
        'Pengurus Masjid',
        'Donatur',
        'Masyarakat Sekitar',
    ];

    public const PROMPT_TINGKAT_KERAMAIAN_OPTIONS = [
        'Sangat Minimalis',
        'Rapi Seimbang',
        'Cukup Ramai',
        'Meriah',
        'Formal Bersih',
    ];

    public const PROMPT_FOKUS_UTAMA_OPTIONS = [
        'Tema Materi',
        'Nama Pemateri',
        'Tanggal dan Waktu',
        'Nama Kegiatan',
        'Lokasi',
        'Ajakan Menghadiri',
        'Logo dan Identitas Masjid',
    ];

    public const PROMPT_ELEMEN_DESAIN_OPTIONS = [
        'Ornamen Geometris Islami',
        'Siluet Masjid',
        'Kubah Masjid',
        'Mihrab',
        'Gradasi Warna',
        'Tekstur Halus',
        'Efek Glow',
        'Bulan Sabit',
        'Bintang',
        'Lentera Ramadhan',
        'Motif Arabesque',
        'Tekstur Kertas Premium',
        'Pola Cahaya Lembut',
        'Background Masjid',
        'Bingkai Elegan',
        'Kaligrafi Dekoratif',
        'Ilustrasi Jamaah',
        'Ikon Kalender',
        'Ikon Lokasi',
        'Ikon Jam',
        'Area Foto Narasumber',
        'Area Logo Masjid',
        'QR Code',
        'Garis Aksen Emas',
        'Gradasi Hijau Tua',
        'Ruang Kosong yang Lega',
        'Frame Judul Dekoratif',
        'Panel Nama Pemateri',
        'Foto Pemateri Besar',
        'Dua Foto Pemateri Berdampingan',
        'Blok Informasi Tanggal',
        'Blok Informasi Lokasi',
        'Blok Informasi Waktu',
        'Badge Ajakan',
        'Ikon WhatsApp',
        'Area Hotline',
        'Logo Penyelenggara Atas',
        'Background Gradasi Islami',
        'Cahaya Lembut di Belakang Foto',
        'Ornamen Bulan dan Bintang',
        'Shape Lengkung Modern',
        'Panel Informasi Transparan',
    ];

    public const LABEL_KONTAK_OPTIONS = [
        'Narahubung',
        'Info Pendaftaran',
        'Konfirmasi Kehadiran',
        'Kontak Panitia',
        'Hubungi Panitia',
    ];

    public const PROMPT_POSISI_FOTO_OPTIONS = [
        'Kanan',
        'Kiri',
        'Bawah',
        'Tengah bawah',
        'Dalam frame lingkaran',
        'Dalam kartu profil',
    ];

    public const PROMPT_TUJUAN_FLYER_OPTIONS = [
        'Mengajak Hadir',
        'Pendaftaran Peserta',
        'Pengumuman Kegiatan',
        'Reminder Acara',
        'Publikasi Dokumentasi',
    ];

    public const PROMPT_MODEL_LAYOUT_OPTIONS = [
        'Seimbang',
        'Judul Besar Tengah',
        'Informasi Acara Dominan',
        'Foto Pemateri Dominan',
        'Foto Narasumber Dominan',
        'Dua Pemateri Berdampingan',
        'Logo Masjid Dominan',
        'Kontak Pendaftaran Dominan',
        'Minimalis Formal',
        'Kajian Premium Modern',
    ];

    public const PROMPT_KEPADATAN_TEKS_OPTIONS = [
        'Sangat Ringkas',
        'Normal',
        'Detail Tapi Tetap Rapi',
    ];

    protected $fillable = [
        'mosque_id',
        'nama_kegiatan',
        'jenis_kegiatan',
        'tema_materi',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'penanggung_jawab',
        'narasumber',
        'target_peserta',
        'kontak_person',
        'nomor_kontak',
        'label_kontak',
        'status',
        'deskripsi',
        'catatan',
        'tampilkan_di_website',
        'status_publik',
        'judul_publik',
        'deskripsi_publik',
        'poster_publik',
        'prompt_nuansa_desain',
        'prompt_warna_utama',
        'prompt_gaya_desain',
        'prompt_catatan_khusus',
        'prompt_instruksi_foto',
        'prompt_pakai_foto_narasumber',
        'prompt_posisi_foto_pemateri',
        'prompt_tujuan_flyer',
        'prompt_model_layout',
        'prompt_kepadatan_teks',
        'prompt_target_audiens',
        'prompt_tingkat_keramaian',
        'prompt_fokus_utama',
        'prompt_elemen_desain',
        'prompt_catatan_tambahan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'tampilkan_di_website' => 'boolean',
        'prompt_pakai_foto_narasumber' => 'boolean',
        'prompt_elemen_desain' => 'array',
    ];

    public function jadwalPetugas(): HasMany
    {
        return $this->hasMany(JadwalPetugas::class, 'kegiatan_id');
    }

    public static function jenisOptions(): array
    {
        return self::JENIS_OPTIONS;
    }

    public static function promptNuansaOptions(): array
    {
        return self::PROMPT_NUANSA_OPTIONS;
    }

    public static function promptWarnaOptions(): array
    {
        return self::PROMPT_WARNA_OPTIONS;
    }

    public static function promptGayaOptions(): array
    {
        return self::PROMPT_GAYA_OPTIONS;
    }

    public static function promptTargetAudiensOptions(): array
    {
        return self::PROMPT_TARGET_AUDIENS_OPTIONS;
    }

    public static function promptTingkatKeramaianOptions(): array
    {
        return self::PROMPT_TINGKAT_KERAMAIAN_OPTIONS;
    }

    public static function promptFokusUtamaOptions(): array
    {
        return self::PROMPT_FOKUS_UTAMA_OPTIONS;
    }

    public static function promptElemenDesainOptions(): array
    {
        return self::PROMPT_ELEMEN_DESAIN_OPTIONS;
    }

    public static function labelKontakOptions(): array
    {
        return self::LABEL_KONTAK_OPTIONS;
    }

    public static function promptPosisiFotoOptions(): array
    {
        return self::PROMPT_POSISI_FOTO_OPTIONS;
    }

    public static function promptTujuanFlyerOptions(): array
    {
        return self::PROMPT_TUJUAN_FLYER_OPTIONS;
    }

    public static function promptModelLayoutOptions(): array
    {
        return self::PROMPT_MODEL_LAYOUT_OPTIONS;
    }

    public static function promptKepadatanTeksOptions(): array
    {
        return self::PROMPT_KEPADATAN_TEKS_OPTIONS;
    }
}
