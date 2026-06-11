<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Penerimaan ZIS</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 12px;
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
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 18mm;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
        }

        .header {
            text-align: center;
        }

        .mosque-name {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
        }

        .mosque-detail {
            color: #4b5563;
            font-size: 12px;
            margin: 2px 0;
        }

        .divider {
            border-top: 2px solid #111827;
            margin: 14px 0 18px;
        }

        .title {
            font-size: 17px;
            font-weight: 800;
            margin: 20px 0 6px;
            text-align: center;
            text-transform: uppercase;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 5px 0;
            vertical-align: top;
        }

        td.label {
            color: #374151;
            width: 38%;
        }

        td.separator {
            width: 18px;
        }

        .amount {
            font-size: 20px;
            font-weight: 800;
        }

        .note {
            border-top: 1px dashed #d1d5db;
            color: #374151;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 14px;
            text-align: center;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-top: 42px;
            text-align: center;
        }

        .signature-space {
            height: 72px;
        }

        .signature-role {
            color: #374151;
            font-size: 13px;
        }

        .signature-name {
            border-top: 1px solid #111827;
            display: inline-block;
            min-width: 160px;
            padding-top: 6px;
        }

        @media print {
            @page {
                size: A5;
                margin: 0;
            }

            body {
                background: #fff;
            }

            .toolbar {
                display: none;
            }

            .receipt {
                box-shadow: none;
                margin: 0;
                min-height: auto;
                width: 148mm;
            }
        }
    </style>
</head>
<body>
    @php
        $receiptAmount = $receipt->amount ?? $receipt->nominal_uang ?? 0;
        $petugas = $receipt->created_by ?: $receipt->diterima_oleh;
    @endphp

    <div class="toolbar">
        <button type="button" class="button button-primary" onclick="window.print()">Print</button>
        <a href="{{ route('zis.receipts.show', $receipt) }}" class="button">Kembali</a>
    </div>

    <main class="receipt">
        <header class="header">
            <p class="mosque-name">{{ $activeMosque?->name ?? 'Masjid' }}</p>
            @if($activeMosque?->address)
                <p class="mosque-detail">{{ $activeMosque->address }}</p>
            @endif
            @if($activeMosque?->phone)
                <p class="mosque-detail">Telp. {{ $activeMosque->phone }}</p>
            @endif
        </header>

        <div class="divider"></div>

        <h1 class="title">Kwitansi Penerimaan ZIS</h1>

        <table>
            <tr>
                <td class="label">Nomor Kwitansi</td>
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

        <p class="note">Terima kasih. Semoga Allah menerima dan membalas kebaikan Anda.</p>

        <section class="signatures">
            <div>
                <p>Diserahkan oleh,</p>
                <p class="signature-role">Donatur/Muzakki</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $receipt->donor_name ?: '' }}</p>
            </div>
            <div>
                <p>Diterima oleh,</p>
                <p class="signature-role">Petugas Masjid</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $petugas ?: '' }}</p>
            </div>
        </section>
    </main>
</body>
</html>
