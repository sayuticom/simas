<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Tanda Terima ZIS</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 15px;
            line-height: 1.55;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 16px;
        }

        .button {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            color: #374151;
            cursor: pointer;
            display: inline-block;
            font-weight: 700;
            padding: 10px 16px;
            text-decoration: none;
        }

        .button-primary {
            background: #047857;
            border-color: #047857;
            color: #fff;
        }

        .receipt {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            margin: 0 auto 28px;
            max-width: 760px;
            overflow: hidden;
        }

        .header {
            background: #064e3b;
            color: #fff;
            padding: 28px 28px 24px;
            text-align: center;
        }

        .logo {
            background: #fff;
            border-radius: 999px;
            display: inline-flex;
            height: 76px;
            margin-bottom: 12px;
            overflow: hidden;
            padding: 8px;
            width: 76px;
        }

        .logo img {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .mosque-name {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
        }

        .mosque-detail {
            color: #d1fae5;
            font-size: 13px;
            margin: 3px 0;
        }

        .body {
            padding: 28px;
        }

        .status {
            background: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 999px;
            color: #166534;
            display: inline-flex;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 12px;
            text-transform: uppercase;
        }

        .verification {
            align-items: center;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            display: flex;
            gap: 18px;
            justify-content: space-between;
            margin: 0 0 22px;
            padding: 16px;
        }

        .verification-text {
            min-width: 0;
        }

        .verification-title {
            font-size: 15px;
            font-weight: 800;
            margin: 0 0 4px;
        }

        .verification-note {
            color: #4b5563;
            font-size: 13px;
            margin: 0;
        }

        .qr-box {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            flex: 0 0 auto;
            padding: 10px;
            text-align: center;
        }

        .qr-caption {
            color: #4b5563;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.35;
            margin: 7px 0 0;
            max-width: 150px;
        }

        .title {
            font-size: 24px;
            font-weight: 800;
            margin: 16px 0 4px;
            text-transform: uppercase;
        }

        .subtitle {
            color: #4b5563;
            margin: 0 0 22px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 11px 0;
            vertical-align: top;
        }

        td.label {
            color: #4b5563;
            width: 38%;
        }

        td.separator {
            color: #6b7280;
            width: 18px;
        }

        .amount {
            color: #065f46;
            font-size: 22px;
            font-weight: 800;
        }

        .thanks {
            background: #f0fdf4;
            border-radius: 14px;
            color: #14532d;
            font-weight: 700;
            margin-top: 24px;
            padding: 16px;
            text-align: center;
        }

        .signature {
            margin-top: 34px;
            text-align: right;
        }

        .signature-space {
            height: 64px;
        }

        @media (max-width: 640px) {
            body {
                font-size: 14px;
            }

            .toolbar {
                flex-direction: column;
                padding: 12px;
            }

            .button {
                text-align: center;
            }

            .receipt {
                border-radius: 0;
                margin-bottom: 0;
                min-height: 100vh;
            }

            .header,
            .body {
                padding-left: 18px;
                padding-right: 18px;
            }

            .title {
                font-size: 21px;
            }

            .verification {
                align-items: flex-start;
                flex-direction: column;
            }

            td.label {
                width: 42%;
            }
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body {
                background: #fff;
            }

            .toolbar {
                display: none;
            }

            .receipt {
                border-radius: 0;
                box-shadow: none;
                margin: 0;
                max-width: none;
            }

            .header {
                background: #fff;
                border-bottom: 2px solid #111827;
                color: #111827;
                padding-top: 0;
            }

            .mosque-detail {
                color: #374151;
            }
        }
    </style>
</head>
<body>
    @php
        $receiptAmount = $receipt->amount ?? $receipt->nominal_uang ?? 0;
        $petugas = $receipt->created_by ?: $receipt->diterima_oleh;
        $logo = $activeMosque?->profile?->logo;
        $publicReceiptUrl = route('zis.penerimaan.receipt.public', $receipt->public_receipt_token);
    @endphp

    <div class="toolbar">
        @if(auth()->check())
            <a href="{{ route('zis.receipts.index') }}" class="button">Kembali ke Penerimaan ZIS</a>
        @endif
        <button type="button" class="button button-primary" onclick="window.print()">Cetak</button>
        <button type="button" class="button" onclick="copyReceiptUrl()">Salin Link</button>
    </div>

    <main class="receipt">
        <header class="header">
            @if($logo)
                <span class="logo"><img src="{{ asset('storage/' . $logo) }}" alt="Logo {{ $activeMosque?->name ?? 'Masjid' }}"></span>
            @endif
            <p class="mosque-name">{{ $activeMosque?->name ?? 'Masjid' }}</p>
            @if($activeMosque?->address)
                <p class="mosque-detail">{{ $activeMosque->address }}</p>
            @endif
            @if($activeMosque?->phone)
                <p class="mosque-detail">Telp. {{ $activeMosque->phone }}</p>
            @endif
        </header>

        <section class="body">
            <div class="verification">
                <div class="verification-text">
                    <span class="status">Sah / Tercatat di Sistem</span>
                    <h1 class="title">Bukti Tanda Terima ZIS</h1>
                    <p class="subtitle">Bukti digital ini diterbitkan oleh sistem SIMAS berdasarkan data penerimaan masjid.</p>
                    <p class="verification-title">Verifikasi bukti digital</p>
                    <p class="verification-note">QR Code ini mengarah ke halaman bukti digital yang sama.</p>
                </div>
                <div class="qr-box">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->margin(1)->generate($publicReceiptUrl) !!}
                    <p class="qr-caption">Scan untuk verifikasi bukti penerimaan digital.</p>
                </div>
            </div>

            <table>
                <tr>
                    <td class="label">Nomor Tanda Terima</td>
                    <td class="separator">:</td>
                    <td><strong>{{ $receiptNumber }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Penerimaan</td>
                    <td class="separator">:</td>
                    <td>{{ $receipt->receipt_date?->format('d-m-Y') ?? $receipt->tanggal?->format('d-m-Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Donatur/Muzakki</td>
                    <td class="separator">:</td>
                    <td>{{ $receipt->donor_name ?: 'Tidak dicantumkan' }}</td>
                </tr>
                <tr>
                    <td class="label">No. HP</td>
                    <td class="separator">:</td>
                    <td>{{ $receipt->donor_phone ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori ZIS</td>
                    <td class="separator">:</td>
                    <td>{{ $receipt->category?->name ?? $receipt->jenis_penerimaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat Dana / Akun Kas</td>
                    <td class="separator">:</td>
                    <td>{{ $receipt->cashAccount ? $receipt->cashAccount->name.' - '.$receipt->cashAccount->accountTypeLabel() : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nominal</td>
                    <td class="separator">:</td>
                    <td class="amount">Rp {{ number_format((float) $receiptAmount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Keterangan</td>
                    <td class="separator">:</td>
                    <td>{{ $receipt->description ?: $receipt->keterangan ?: '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Petugas/Penerima</td>
                    <td class="separator">:</td>
                    <td>{{ $petugas ?: '-' }}</td>
                </tr>
            </table>

            <p class="thanks">Terima kasih. Semoga Allah menerima dan membalas kebaikan Anda.</p>

            <div class="signature">
                <p>Diterima oleh,</p>
                <p>Petugas Masjid</p>
                <div class="signature-space"></div>
                <p><strong>{{ $petugas ?: '' }}</strong></p>
            </div>
        </section>
    </main>

    <script>
        function copyReceiptUrl() {
            navigator.clipboard.writeText(window.location.href);
        }
    </script>
</body>
</html>
