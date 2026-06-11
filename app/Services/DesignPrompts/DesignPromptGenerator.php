<?php

namespace App\Services\DesignPrompts;

use App\Models\DesignPromptTemplate;
use App\Models\DonationProgram;
use App\Models\Kegiatan;
use App\Support\DesignPromptOptions;

class DesignPromptGenerator
{
    public const SOURCE_KEGIATAN = 'kegiatan';
    public const SOURCE_DONASI = 'donasi';

    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_KEGIATAN => 'Kegiatan',
            self::SOURCE_DONASI => 'Donasi',
        ];
    }

    public function getSourceData(?string $sourceType, ?int $sourceId, int $mosqueId): array
    {
        if (! $sourceType || ! $sourceId) {
            return [];
        }

        return match ($sourceType) {
            self::SOURCE_KEGIATAN => $this->kegiatanData($sourceId, $mosqueId),
            self::SOURCE_DONASI => $this->donasiData($sourceId, $mosqueId),
            default => abort(422, 'Tipe sumber prompt desain tidak didukung.'),
        };
    }

    public function buildPrompt(DesignPromptTemplate $template, array $sourceData, array $options = []): string
    {
        $prompt = $template->prompt_structure;
        $placeholders = $this->placeholderValues($sourceData);

        foreach ($placeholders as $key => $value) {
            $prompt = str_replace('{'.$key.'}', (string) $value, $prompt);
        }

        $details = array_filter([
            'Nuansa' => $template->tone,
            'Gaya' => $template->style,
            'Palet warna' => $template->color_palette,
            'Target audiens' => $template->target_audience,
            'Kepadatan layout' => $template->layout_density,
            'Platform' => $this->listValue($template->platforms),
            'Elemen desain' => $this->listValue($template->elements),
            'Aturan teks wajib' => $this->listValue($template->required_text_rules),
            'Aturan foto' => $this->listValue($template->photo_rules),
        ]);

        if ($details) {
            $prompt .= "\n\nArahan template:\n";
            foreach ($details as $label => $value) {
                $prompt .= "- {$label}: {$value}\n";
            }
        }

        if ($options) {
            $prompt .= "\nOpsi tambahan:\n";
            foreach ($options as $label => $value) {
                if (is_array($value)) {
                    $value = $this->listValue($value);
                }

                if ($value !== null && $value !== '') {
                    $prompt .= '- '.str_replace('_', ' ', (string) $label).": {$value}\n";
                }
            }
        }

        return trim($prompt);
    }

    public function buildPromptFromOptions(string $sourceType, int $sourceId, int $mosqueId, array $options = []): string
    {
        $sourceData = $this->getSourceData($sourceType, $sourceId, $mosqueId);

        return $this->buildPromptText($sourceData, $options);
    }

    public function buildPromptText(array $sourceData, array $options = []): string
    {
        $module = $sourceData['module_type'] ?? 'umum';
        $title = $sourceData['judul'] ?? 'konten masjid';
        $purpose = $options['prompt_tujuan_flyer'] ?? ($module === self::SOURCE_DONASI ? 'Ajakan Donasi' : 'Mengajak Hadir');
        $tone = $options['prompt_nuansa_desain'] ?? 'Islami Modern';
        $style = $options['prompt_gaya_desain'] ?? 'Modern Minimalis';
        $colors = $options['prompt_warna_utama'] ?? 'Hijau Tua, Putih, Emas';
        $audience = $options['prompt_target_audiens'] ?? ($module === self::SOURCE_DONASI ? 'Donatur' : 'Jamaah Umum');
        $focus = $options['prompt_fokus_utama'] ?? ($module === self::SOURCE_DONASI ? 'Target Donasi' : 'Nama Kegiatan');
        $layout = $options['prompt_model_layout'] ?? 'Seimbang';
        $crowdLevel = $options['prompt_tingkat_keramaian'] ?? null;
        $textDensity = $options['prompt_kepadatan_teks'] ?? null;
        $elements = $options['prompt_elemen_desain'] ?? [];
        $autoElements = DesignPromptOptions::automaticInformationElements($module);
        $allElements = array_values(array_unique(array_filter(array_merge(is_array($elements) ? $elements : [], $autoElements))));
        $usesPhoto = (string) ($options['prompt_pakai_foto_narasumber'] ?? '0') === '1';
        $photoPosition = $options['prompt_posisi_foto_pemateri'] ?? null;
        $note = trim((string) ($options['prompt_catatan_tambahan'] ?? ''));

        $openingSubject = $module === self::SOURCE_DONASI ? 'donasi masjid' : 'kegiatan masjid';
        $lines = [];
        $lines[] = "Buatkan desain flyer/poster {$openingSubject} dalam bahasa Indonesia untuk media sosial ukuran 1080 x 1080 px dengan rasio 1:1. Desain harus terlihat resmi, rapi, menarik, mudah dibaca di layar HP, dan cocok dibagikan ke Instagram, Facebook, WhatsApp, serta website.";
        $lines[] = '';
        $lines[] = "Poster ini bertujuan sebagai {$this->lower($purpose)} untuk \"{$title}\".";
        $lines[] = '';
        $visualSentence = "Gunakan nuansa {$this->lower($tone)} dengan gaya {$this->lower($style)}. Warna utama menggunakan {$colors}. Target audiens adalah {$this->lower($audience)}, sehingga desain harus terasa amanah, profesional, dan mudah dipahami.";
        if ($crowdLevel || $textDensity) {
            $visualSentence .= ' Komposisi dibuat '.trim(implode(' dan ', array_filter([$this->lower($crowdLevel), 'teks '.$this->lower($textDensity)]))).'.';
        }
        $lines[] = $visualSentence;
        $lines[] = '';

        if ($module === self::SOURCE_DONASI) {
            $lines[] = "Fokus utama poster adalah {$this->lower($focus)}. Buat target donasi, progress donasi, rekening donasi, dan QRIS jika tersedia tampil jelas sebagai informasi utama.";
        } else {
            $lines[] = "Fokus utama poster adalah {$this->lower($focus)}. Buat judul, tema, tanggal, jam, lokasi, dan narasumber jika tersedia mudah ditemukan dalam satu pandangan.";
        }
        $lines[] = '';
        $lines[] = 'Informasi wajib yang harus tampil:';

        foreach ($this->sourceInformationLines($sourceData) as $line) {
            $lines[] = $line;
        }

        $lines[] = '';
        $layoutSentence = "Susun layout dengan model {$this->lower($layout)}. Bagian atas digunakan untuk identitas atau logo masjid, judul dibuat besar dan dominan, informasi utama diletakkan di area yang mudah terlihat, dan deskripsi dibuat singkat agar tidak terlalu padat.";
        if ($module === self::SOURCE_DONASI) {
            $layoutSentence .= ' Progress donasi, nomor rekening, dan QRIS dibuat jelas, besar, dan mudah ditemukan.';
        } elseif ($module === self::SOURCE_KEGIATAN) {
            $layoutSentence .= ' Tanggal, waktu, lokasi, dan kontak jika tersedia dibuat dalam blok informasi yang rapi.';
        }
        $lines[] = '';
        $lines[] = $layoutSentence;
        $lines[] = '';
        if ($allElements) {
            $lines[] = 'Gunakan elemen pendukung seperlunya seperti '.implode(', ', $allElements).'. Elemen pendukung tidak harus semua tampil besar; tempatkan secukupnya agar memperkuat suasana visual tanpa mengganggu keterbacaan informasi utama.';
            $lines[] = '';
        }

        if ($usesPhoto) {
            $position = $photoPosition ? ' dengan posisi '.$this->lower($photoPosition) : '';
            $speakerName = ! empty($sourceData['narasumber']) ? ' Jika nama narasumber tersedia, tampilkan nama "'.$sourceData['narasumber'].'" di bawah foto dengan rapi.' : '';
            $lines[] = "Gunakan foto narasumber yang diberikan{$position}. Jangan mengubah wajah, identitas visual, atau ekspresi utama narasumber secara berlebihan.{$speakerName}";
        } else {
            $lines[] = 'Jangan tampilkan foto narasumber. Jika ada nama narasumber, tampilkan sebagai teks biasa yang rapi.';
        }

        if ($note !== '') {
            $lines[] = '';
            $lines[] = 'Preferensi visual tambahan: '.$note;
        }

        $lines[] = '';
        $lines[] = 'Jangan menambahkan informasi yang tidak tersedia dalam data. Hindari teks terlalu kecil, warna abu-abu muda, atau kontras yang lemah. Pastikan semua informasi penting terbaca jelas di layar HP.';

        return trim(implode("\n", array_filter($lines, fn ($line) => $line !== null)));
    }

    public function buildTitle(array $sourceData): string
    {
        $title = $sourceData['judul'] ?? null;
        $module = $sourceData['module_label'] ?? 'Umum';

        return $title ? "Prompt {$module}: {$title}" : 'Prompt Desain Baru';
    }

    private function kegiatanData(int $sourceId, int $mosqueId): array
    {
        $kegiatan = Kegiatan::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->findOrFail($sourceId);

        return [
            'source_type' => self::SOURCE_KEGIATAN,
            'source_id' => $kegiatan->id,
            'module_type' => self::SOURCE_KEGIATAN,
            'module_label' => 'Kegiatan',
            'judul' => $kegiatan->nama_kegiatan,
            'deskripsi' => $kegiatan->deskripsi_publik ?: $kegiatan->deskripsi,
            'jenis_kegiatan' => $kegiatan->jenis_kegiatan,
            'tema_materi' => $kegiatan->tema_materi,
            'tanggal' => $kegiatan->tanggal_mulai?->format('d F Y'),
            'jam' => $this->timeRange($kegiatan->tanggal_mulai, $kegiatan->tanggal_selesai),
            'lokasi' => $kegiatan->lokasi,
            'narasumber' => $kegiatan->narasumber,
            'target_audiens' => $kegiatan->target_peserta,
            'kontak_person' => $kegiatan->kontak_person,
            'nomor_kontak' => $kegiatan->nomor_kontak,
            'label_kontak' => $kegiatan->label_kontak,
            'image_path' => $kegiatan->poster_publik,
        ];
    }

    private function donasiData(int $sourceId, int $mosqueId): array
    {
        $program = DonationProgram::withoutGlobalScope('mosque')
            ->where('mosque_id', $mosqueId)
            ->findOrFail($sourceId);

        return [
            'source_type' => self::SOURCE_DONASI,
            'source_id' => $program->id,
            'module_type' => self::SOURCE_DONASI,
            'module_label' => 'Donasi',
            'judul' => $program->title,
            'deskripsi' => $program->description,
            'category' => $program->category,
            'target_amount' => $this->rupiah($program->target_amount),
            'collected_amount' => $this->rupiah($program->collected_amount),
            'bank_name' => $program->bank_name,
            'bank_account_number' => $program->bank_account_number,
            'bank_account_name' => $program->bank_account_name,
            'qris' => $program->qris_image ? 'QRIS tersedia' : '',
            'whatsapp_number' => $program->whatsapp_number,
            'image_path' => $program->featured_image,
        ];
    }

    private function placeholderValues(array $sourceData): array
    {
        return [
            'judul' => $sourceData['judul'] ?? '',
            'deskripsi' => $sourceData['deskripsi'] ?? '',
            'module_type' => $sourceData['module_type'] ?? '',
            'nama_masjid' => auth()->user()?->getActiveMosque()?->name ?? '',
            'jenis_kegiatan' => $sourceData['jenis_kegiatan'] ?? '',
            'tema_materi' => $sourceData['tema_materi'] ?? '',
            'tanggal' => $sourceData['tanggal'] ?? '',
            'jam' => $sourceData['jam'] ?? '',
            'lokasi' => $sourceData['lokasi'] ?? '',
            'narasumber' => $sourceData['narasumber'] ?? '',
            'target_audiens' => $sourceData['target_audiens'] ?? '',
            'category' => $sourceData['category'] ?? '',
            'target_amount' => $sourceData['target_amount'] ?? '',
            'collected_amount' => $sourceData['collected_amount'] ?? '',
            'bank_name' => $sourceData['bank_name'] ?? '',
            'bank_account_number' => $sourceData['bank_account_number'] ?? '',
            'bank_account_name' => $sourceData['bank_account_name'] ?? '',
            'rekening' => trim(implode(' ', array_filter([
                $sourceData['bank_name'] ?? null,
                $sourceData['bank_account_number'] ?? null,
                $sourceData['bank_account_name'] ?? null,
            ]))),
            'qris' => $sourceData['qris'] ?? '',
        ];
    }

    private function sourceInformationLines(array $sourceData): array
    {
        $map = [
            'Nama/Judul' => $sourceData['judul'] ?? null,
            'Jenis kegiatan' => $sourceData['jenis_kegiatan'] ?? null,
            'Tema materi' => $sourceData['tema_materi'] ?? null,
            'Narasumber' => $sourceData['narasumber'] ?? null,
            'Tanggal' => $sourceData['tanggal'] ?? null,
            'Waktu' => $sourceData['jam'] ?? null,
            'Lokasi' => $sourceData['lokasi'] ?? null,
            'Kategori' => $sourceData['category'] ?? null,
            'Target dana' => $sourceData['target_amount'] ?? null,
            'Dana terkumpul' => $sourceData['collected_amount'] ?? null,
            'Rekening' => trim(implode(' ', array_filter([$sourceData['bank_name'] ?? null, $sourceData['bank_account_number'] ?? null, $sourceData['bank_account_name'] ?? null]))),
            'QRIS' => $sourceData['qris'] ?? null,
            'Kontak' => trim(implode(' ', array_filter([$sourceData['label_kontak'] ?? null, $sourceData['kontak_person'] ?? null, $sourceData['nomor_kontak'] ?? null, $sourceData['whatsapp_number'] ?? null]))),
            'Masjid/Penyelenggara' => auth()->user()?->getActiveMosque()?->name ?? null,
            'Deskripsi singkat' => $sourceData['deskripsi'] ?? null,
        ];

        return collect($map)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, $label) => "- {$label}: {$value}")
            ->values()
            ->all();
    }

    private function listValue(mixed $value): string
    {
        if (is_array($value)) {
            return implode(', ', array_filter(array_map('strval', $value)));
        }

        return (string) $value;
    }

    private function timeRange(mixed $start, mixed $end): string
    {
        $startTime = $start?->format('H:i');
        $endTime = $end?->format('H:i');

        return trim(implode(' - ', array_filter([$startTime, $endTime])));
    }

    private function rupiah(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }

    private function lower(?string $value): string
    {
        return $value ? mb_strtolower($value) : '';
    }
}
