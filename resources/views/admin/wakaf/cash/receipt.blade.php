<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Tanda Terima Wakaf Tunai</title>
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
            background: #4f46e5;
            border-color: #4f46e5;
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

        .title {
            font-size: 17px;
            font-weight: 800;
            margin: 20px 0 6px;
            text-align: center;
            text-transform: uppercase;
        }

        .divider {
            border-top: 2px solid #111827;
            margin: 14px 0 18px;
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
            margin-top: 18px;
            color: #374151;
            font-size: 12px;
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
        $paymentLabels = [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'ewallet' => 'E-Wallet',
            'lainnya' => 'Lainnya',
        ];
    @endphp

    <div class="toolbar">
        <button type="button" class="button button-primary" onclick="window.print()">Print</button>
        <a href="{{ route('wakaf.cash.show', $wakafCash) }}" class="button">Kembali</a>
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

        <h1 class="title">Bukti Tanda Terima Wakaf Tunai</h1>

        <table>
            <tr>
                <td class="label">Nomor Bukti</td>
                <td class="separator">:</td>
                <td><strong>{{ $receiptNumber }}</strong></td>
            </tr>
            <tr>
                <td class="label">Tanggal Terima</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->tanggal_terima?->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nama Wakif</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->wakif?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No HP Wakif</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->wakif?->no_hp ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Nazhir</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->nazhir?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Program Wakaf</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->program?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nominal</td>
                <td class="separator">:</td>
                <td class="amount">Rp {{ number_format((float) $wakafCash->nominal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Terbilang</td>
                <td class="separator">:</td>
                <td>{{ $terbilang }}</td>
            </tr>
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td class="separator">:</td>
                <td>{{ $paymentLabels[$wakafCash->metode_pembayaran] ?? ($wakafCash->metode_pembayaran ?: '-') }}</td>
            </tr>
            <tr>
                <td class="label">Akun Penerimaan Dana</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->cashAccount ? $wakafCash->cashAccount->name.' - '.$wakafCash->cashAccount->accountTypeLabel() : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tujuan Investasi</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->tujuan_investasi ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="separator">:</td>
                <td>{{ ucfirst($wakafCash->status ?: 'tercatat') }}</td>
            </tr>
            <tr>
                <td class="label">Keterangan</td>
                <td class="separator">:</td>
                <td>{{ $wakafCash->keterangan ?: '-' }}</td>
            </tr>
        </table>

        <p class="note">
            Bukti ini dibuat sebagai tanda terima uang wakaf tunai dan dicetak dari SIMAS.
        </p>

        <section class="signatures">
            <div>
                <p>Diserahkan oleh,</p>
                <p class="signature-role">Wakif</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $wakafCash->wakif?->nama ?? '' }}</p>
            </div>
            <div>
                <p>Diterima oleh,</p>
                <p class="signature-role">Nazhir/Petugas</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $wakafCash->nazhir?->nama ?? '' }}</p>
            </div>
        </section>
    </main>
</body>
</html>
