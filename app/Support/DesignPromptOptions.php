<?php

namespace App\Support;

class DesignPromptOptions
{
    public static function photoUsageOptions(): array
    {
        return [
            '0' => 'Tidak pakai foto narasumber',
            '1' => 'Pakai foto narasumber',
        ];
    }

    public static function flyerPurposeOptions(): array
    {
        return ['Mengajak Hadir', 'Pendaftaran Peserta', 'Pengumuman Kegiatan', 'Reminder Acara', 'Publikasi Dokumentasi', 'Ajakan Donasi'];
    }

    public static function designToneOptions(): array
    {
        return ['Islami Modern', 'Elegan dan Resmi', 'Hangat dan Ramah Keluarga', 'Semangat Anak Muda', 'Khidmat dan Tenang', 'Ceria dan Edukatif', 'Sosial Kemanusiaan', 'Ramadhan', 'Idul Fitri', 'Idul Adha'];
    }

    public static function mainColorOptions(): array
    {
        return ['Hijau Tua, Putih, Emas', 'Hijau Toska, Putih, Abu Terang', 'Biru Navy, Putih, Emas', 'Maroon, Krem, Emas', 'Hitam, Emas, Putih', 'Putih, Hijau, Krem', 'Merah Putih', 'Warna Natural Bumi'];
    }

    public static function designStyleOptions(): array
    {
        return ['Modern Minimalis', 'Formal Resmi', 'Poster Kajian Ilmiah', 'Flyer Dakwah Elegan', 'Desain Remaja Kreatif', 'Desain Ramadhan', 'Desain Sosial Kemanusiaan', 'Desain Masjid Klasik', 'Desain Tipografi Kuat'];
    }

    public static function targetAudienceOptions(): array
    {
        return ['Jamaah Umum', 'Ikhwan', 'Akhwat', 'Remaja', 'Anak-anak', 'Keluarga', 'Pengurus Masjid', 'Donatur', 'Masyarakat Sekitar'];
    }

    public static function crowdLevelOptions(): array
    {
        return ['Sangat Minimalis', 'Rapi Seimbang', 'Cukup Ramai', 'Meriah', 'Formal Bersih'];
    }

    public static function mainFocusOptions(): array
    {
        return ['Tema Materi', 'Nama Pemateri', 'Tanggal dan Waktu', 'Nama Kegiatan', 'Lokasi', 'Ajakan Menghadiri', 'Logo dan Identitas Masjid', 'Target Donasi', 'Nomor Rekening/QRIS'];
    }

    public static function layoutModelOptions(): array
    {
        return ['Seimbang', 'Judul Besar Tengah', 'Informasi Acara Dominan', 'Foto Pemateri Dominan', 'Foto Narasumber Dominan', 'Dua Pemateri Berdampingan', 'Logo Masjid Dominan', 'Kontak Pendaftaran Dominan', 'Minimalis Formal', 'Kajian Premium Modern', 'Donasi Progress Dominan'];
    }

    public static function textDensityOptions(): array
    {
        return ['Sangat Ringkas', 'Normal', 'Detail Tapi Tetap Rapi'];
    }

    public static function speakerPhotoPositionOptions(): array
    {
        return ['Kanan', 'Kiri', 'Bawah', 'Tengah bawah', 'Dalam frame lingkaran', 'Dalam kartu profil'];
    }

    public static function designElementOptions(): array
    {
        return [
            'Ornamen Geometris Islami', 'Siluet Masjid', 'Kubah Masjid', 'Mihrab', 'Gradasi Warna', 'Tekstur Halus', 'Efek Glow', 'Cahaya Lembut',
            'Bulan Sabit', 'Bintang', 'Lentera Ramadhan', 'Motif Arabesque', 'Tekstur Kertas Premium', 'Pola Cahaya Lembut',
            'Background Masjid', 'Bingkai Elegan', 'Kaligrafi Dekoratif', 'Ilustrasi Jamaah', 'Ikon Kalender', 'Ikon Lokasi',
            'Ikon Jam', 'Area Foto Narasumber', 'Area Logo Masjid', 'QR Code', 'Garis Aksen Emas', 'Gradasi Hijau Tua',
            'Ruang Kosong yang Lega', 'Frame Judul Dekoratif', 'Panel Nama Pemateri', 'Foto Pemateri Besar',
            'Dua Foto Pemateri Berdampingan', 'Blok Informasi Tanggal', 'Blok Informasi Lokasi', 'Blok Informasi Waktu',
            'Badge Ajakan', 'Ikon WhatsApp', 'Area Hotline', 'Logo Penyelenggara Atas', 'Background Gradasi Islami',
            'Cahaya Lembut di Belakang Foto', 'Ornamen Bulan dan Bintang', 'Shape Lengkung Modern', 'Panel Informasi Transparan',
            'Progress Donasi', 'Area Rekening', 'Area QRIS',
        ];
    }

    public static function groupedDesignElementOptions(): array
    {
        return [
            'Background' => [
                'Background Masjid',
                'Background Gradasi Islami',
                'Gradasi Warna',
                'Tekstur Halus',
                'Tekstur Kertas Premium',
            ],
            'Ornamen Islami' => [
                'Motif Arabesque',
                'Ornamen Geometris Islami',
                'Kubah Masjid',
                'Mihrab',
                'Kaligrafi Dekoratif',
                'Ornamen Bulan dan Bintang',
                'Bintang',
                'Bulan Sabit',
                'Lentera Ramadhan',
            ],
            'Efek Visual' => [
                'Efek Glow',
                'Cahaya Lembut',
                'Pola Cahaya Lembut',
                'Bingkai Elegan',
                'Frame Judul Dekoratif',
                'Shape Lengkung Modern',
                'Panel Informasi Transparan',
                'Garis Aksen Emas',
                'Ruang Kosong yang Lega',
            ],
        ];
    }

    public static function automaticInformationElements(?string $sourceType): array
    {
        return match ($sourceType) {
            'donasi' => ['Progress Donasi', 'Area Rekening', 'QR Code / QRIS jika tersedia', 'Badge Ajakan'],
            'kegiatan' => ['Blok Informasi Tanggal', 'Blok Informasi Waktu', 'Blok Informasi Lokasi', 'Ikon Kalender', 'Ikon Jam', 'Ikon Lokasi', 'Area Hotline / WhatsApp jika tersedia'],
            default => ['Judul utama', 'Ringkasan konten', 'Identitas masjid'],
        };
    }

    public static function all(): array
    {
        return [
            'photoUsageOptions' => self::photoUsageOptions(),
            'flyerPurposeOptions' => self::flyerPurposeOptions(),
            'designToneOptions' => self::designToneOptions(),
            'mainColorOptions' => self::mainColorOptions(),
            'designStyleOptions' => self::designStyleOptions(),
            'targetAudienceOptions' => self::targetAudienceOptions(),
            'crowdLevelOptions' => self::crowdLevelOptions(),
            'mainFocusOptions' => self::mainFocusOptions(),
            'layoutModelOptions' => self::layoutModelOptions(),
            'textDensityOptions' => self::textDensityOptions(),
            'speakerPhotoPositionOptions' => self::speakerPhotoPositionOptions(),
            'designElementOptions' => self::designElementOptions(),
            'groupedDesignElementOptions' => self::groupedDesignElementOptions(),
        ];
    }
}
