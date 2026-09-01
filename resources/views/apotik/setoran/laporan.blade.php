<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Setoran</title>
    <style>
        @page {
            margin: 130px 40px 90px 40px;
            /* ruang untuk header & footer fixed */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        /* ===== HEADER (tampil di setiap halaman) ===== */
        .header {
            position: fixed;
            top: -110px;
            left: 0;
            right: 0;
            height: 100px;
        }

        .header-line {
            border-top: 2px solid #2b6cb0;
            margin: 4px 0;
        }

        .header table {
            width: 100%;
            border: none;
        }

        .header td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .header .logo-cell {
            width: 60px;
        }

        .header .logo-cell img {
            width: 50px;
        }

        .header .name-cell {
            font-weight: bold;
            font-size: 16px;
            line-height: 1.2;
            padding-left: 10px;
        }

        /* ===== FOOTER (tampil di setiap halaman) ===== */
        .footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
        }

        .footer-line {
            border-top: 2px solid #2b6cb0;
            margin-bottom: 4px;
        }

        .footer p {
            margin: 2px 0;
            font-size: 10px;
            color: #333;
        }

        /* ===== BODY ===== */
        h2.title {
            text-align: center;
            margin: 0 0 4px 0;
        }

        p.sub {
            text-align: center;
            margin: 0 0 16px 0;
            color: #444;
        }

        table.info {
            width: 100%;
            margin-bottom: 16px;
        }

        table.info td {
            padding: 2px 4px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.data thead {
            display: table-header-group;
        }

        table.data tbody tr {
            page-break-inside: avoid;
        }

        table.data th,
        table.data td {
            border: 1px solid #333;
            padding: 6px 8px;
        }

        table.data th {
            background: #dbe4f7;
            text-align: left;
        }

        table.data td.num {
            text-align: right;
        }

        table.summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            page-break-inside: avoid;
        }

        table.summary td {
            border: 1px solid #333;
            padding: 8px 10px;
        }

        table.summary td.label {
            background: #f2f2f2;
            font-weight: bold;
            width: 60%;
        }

        table.summary td.value {
            text-align: right;
        }

        .pendapatan-wrap {
            page-break-inside: avoid;
        }

        h3.subtitle {
            text-align: center;
            margin: 20px 0 8px 0;
        }

        table.pendapatan {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.pendapatan thead {
            display: table-header-group;
        }

        table.pendapatan tbody tr {
            page-break-inside: avoid;
        }

        table.pendapatan th,
        table.pendapatan td {
            border: 1px solid #333;
            padding: 6px 8px;
        }

        table.pendapatan th {
            background: #dbe4f7;
            text-align: left;
        }

        table.pendapatan td.num {
            text-align: right;
        }

        table.pendapatan tr.total-row td {
            font-weight: bold;
            background: #f2f2f2;
        }

        .selisih-positif {
            color: #1a7f37;
            font-weight: bold;
        }

        .selisih-negatif {
            color: #c0392b;
            font-weight: bold;
        }

        .ttd-wrap {
            margin-top: 50px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .ttd-wrap td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }

        .ttd-space {
            height: 60px;
        }

        .ttd-name {
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 4px;
            font-weight: bold;
            text-decoration: underline;
        }

        .ttd-name-blank {
            border-top: 1px solid #000;
            display: inline-block;
            width: 40%;
            margin: 11 auto;
        }
    </style>
</head>

<body>

    {{-- ===== HEADER (fixed) ===== --}}
    <div class="header">
        <div class="header-line"></div>
        <table>
            <tr>
                <td class="logo-cell">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}">
                    @endif
                </td>
                <td class="name-cell">
                    APOTIK<br>LNG BADAK
                </td>
            </tr>
        </table>
        <div class="header-line"></div>
    </div>

    {{-- ===== FOOTER (fixed) ===== --}}
    <div class="footer">
        <div class="footer-line"></div>
        <p>Jl. Brigjen Katamso RT 05, Bontang Barat - Kalimantan Timur</p>
        <p>poliklinikbadak@gmail.com &nbsp;|&nbsp; 0548-22999 / 0811 5990667</p>
    </div>

    {{-- ===== BODY ===== --}}
    <h2 class="title">LAPORAN SETORAN KASIR</h2>
    <p class="sub">Rekap transaksi tunai dan setoran per shift</p>

    <table class="info">
        <tr>
            <td style="width:120px;">Tanggal</td>
            <td style="width:10px;">:</td>
            <td>{{ $setoran->tanggal->translatedFormat('l, d-m-Y') }}</td>
        </tr>
        <tr>
            <td>Shift</td>
            <td>:</td>
            <td>{{ $setoran->shift }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td>{{ $setoran->user->name ?? '-' }}</td>
        </tr>
    </table>

    {{-- ===== DAFTAR CUSTOMER TUNAI ===== --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>No Registrasi</th>
                <th>Nama Customer</th>
                <th>No HP</th>
                <th class="num">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $i => $customer)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $customer->no_registrasi }}</td>
                    <td>{{ $customer->nama_customer }}</td>
                    <td>{{ $customer->no_hp }}</td>
                    <td class="num">Rp {{ number_format($customer->total_biaya, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Tidak ada transaksi tunai pada shift ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== RINGKASAN SETORAN & SELISIH ===== --}}
    <table class="summary">
        <tr>
            <td class="label">Total Transaksi Tunai Customer (pembulatan)</td>
            <td class="value">Rp {{ number_format($setoran->total_tunai_customer, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Setoran</td>
            <td class="value">Rp {{ number_format($setoran->setoran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Selisih</td>
            <td class="value {{ $setoran->selisih < 0 ? 'selisih-negatif' : 'selisih-positif' }}">
                {{ $setoran->selisih >= 0 ? '+' : '-' }}Rp {{ number_format(abs($setoran->selisih), 0, ',', '.') }}
            </td>
        </tr>
    </table>

    {{-- ===== LAPORAN PENDAPATAN SHIFT (per jenis pembayaran) ===== --}}
    <div class="pendapatan-wrap">
        <h3 class="subtitle">Laporan Pendapatan Shift</h3>
        <table class="pendapatan">
            <thead>
                <tr>
                    <th>Jenis Pembayaran</th>
                    <th class="num">Jumlah Transaksi</th>
                    <th class="num">Nilai Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendapatan as $item)
                    <tr>
                        <td>{{ $item->jenis_pembayaran }}</td>
                        <td class="num">{{ number_format($item->jumlah_transaksi, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($item->nilai_transaksi, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center;">Tidak ada data pendapatan pada shift ini</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td>Total</td>
                    <td class="num">{{ number_format($pendapatan->sum('jumlah_transaksi'), 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($pendapatan->sum('nilai_transaksi'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ===== TTD ===== --}}
    <table class="ttd-wrap">
        <tr>
            <td style="padding-top: 31px">
                <p style="padding: 5px">Kasir Klinik</p>
                <div class="ttd-space"></div>
                <div class="ttd-name-blank">&nbsp;</div>
            </td>
            <td>
                <p>{{ $setoran->tanggal->translatedFormat('l, d-m-Y') }}</p>
                <p>Kasir Apotek</p>
                <div class="ttd-space"></div>
                <p class="ttd-name">{{ $setoran->user->name ?? '-' }}</p>
            </td>
        </tr>
    </table>

</body>

</html>
