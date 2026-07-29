<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Apotek</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 80mm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 6px 20px 24px 15px;
            /* atas | kanan | bawah | kiri */
            color: #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .logo-title {
            font-size: 14px;
            font-weight: bold;
        }

        .sub-title {
            font-size: 11px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .line-solid {
            border-top: 1px solid #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info td {
            padding: 1px 0;
            vertical-align: top;
        }

        table.info td.label {
            width: 38%;
        }

        table.info td.colon {
            width: 4%;
        }

        /* tabel item obat */
        table.items {
            margin-top: 2px;
        }

        table.items thead td {
            font-weight: bold;
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
        }

        table.items tbody td {
            padding: 2px 0;
            vertical-align: top;
        }

        table.items .col-nama {
            width: 46%;
        }

        table.items .col-qty {
            width: 12%;
            text-align: center;
        }

        table.items .col-harga {
            width: 21%;
            text-align: right;
        }

        table.items .col-subtotal {
            width: 21%;
            text-align: right;
        }

        .item-name-row td {
            padding-top: 4px;
            padding-bottom: 0;
            font-weight: bold;
        }

        .item-detail-row td {
            padding-top: 0;
            padding-bottom: 2px;
        }

        table.summary td {
            padding: 2px 0;
        }

        table.summary td.label {
            text-align: left;
        }

        table.summary td.value {
            text-align: right;
        }

        table.summary tr.total-row td {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .footer-note {
            margin-top: 8px;
            font-size: 11px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    {{-- ================= HEADER ================= --}}
    <div class="center">
        <div class="logo-title">APOTEK LNG BADAK</div>
        <div class="sub-title">
            Jl. Brigjen Katamso Kel.Telihan Kec. Bontang Barat, Kota Bontang<br>
            Telp. 0811 5990 667
        </div>
    </div>
    <div class="line-solid"></div>

    {{-- ================= INFO TRANSAKSI ================= --}}
    <table class="info">
        <tr>
            <td class="label">No. Registrasi</td>
            <td class="colon">:</td>
            <td>{{ $customer->no_registrasi }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td>{{ \Carbon\Carbon::parse($customer->tanggal_registrasi)->translatedFormat('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Pasien</td>
            <td class="colon">:</td>
            <td>{{ $customer->nama_customer }}</td>
        </tr>
        @if ($customer->no_hp)
            <tr>
                <td class="label">No. HP</td>
                <td class="colon">:</td>
                <td>{{ $customer->no_hp }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Metode Bayar</td>
            <td class="colon">:</td>
            <td>{{ $customer->metode_bayar }}</td>
        </tr>
    </table>
    <div class="line"></div>

    {{-- ================= DETAIL OBAT ================= --}}
    <table class="items">
        <thead>
            <tr>
                <td class="col-nama">Nama Obat</td>
                <td class="col-qty">Qty</td>
                <td class="col-harga">Harga</td>
                <td class="col-subtotal">Subtotal</td>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($customer->transaksiObat as $item)
                @php
                    $subtotal = $item->jumlah * $item->harga_jual;
                    $grandTotal += $subtotal;
                @endphp
                <tr>
                    <td class="col-nama" colspan="4">{{ $item->nama_obat }}</td>
                </tr>
                <tr>
                    <td class="col-nama"></td>
                    <td class="col-qty">{{ $item->jumlah }}</td>
                    <td class="col-harga">{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td class="col-subtotal">{{ number_format($item->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="line"></div>

    {{-- ================= RINGKASAN TOTAL ================= --}}
    <table class="summary">
        <tr>
            <td class="label">Jumlah Item</td>
            <td class="value">{{ $customer->transaksiObat->sum('jumlah') }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">TOTAL (Real)</td>
            <td class="value">Rp {{ number_format($customer->total_biaya_real, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">TOTAL (Pembulatan)</td>
            <td class="value">Rp {{ number_format($customer->total_biaya, 0, ',', '.') }}</td>
        </tr>

        @if ($customer->metode_bayar === 'TUNAI')
            <tr>
                <td class="label">Tunai</td>
                <td class="value">Rp {{ number_format($customer->uang_tunai, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Kembalian</td>
                <td class="value">Rp {{ number_format($customer->kembalian, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>
    <div class="line"></div>

    {{-- ================= FOOTER ================= --}}
    <div class="center footer-note">
        Terima kasih atas kunjungan Anda<br>
        Semoga lekas sembuh
    </div>

    <div class="no-print center" style="margin-top:12px;">
        <button onclick="window.print()">🖨️ Cetak Ulang</button>
        <button onclick="window.close()">Tutup</button>
    </div>

</body>

</html>
