<?php
// app/Exports/InvoiceReportExport.php
namespace App\Exports;

use App\Models\Customer;
use App\Models\TransaksiObat;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InvoiceReportExport implements WithMultipleSheets
{
    protected $dariTanggal;
    protected $sampaiTanggal;

    public function __construct($dariTanggal, $sampaiTanggal)
    {
        $this->dariTanggal = $dariTanggal;
        $this->sampaiTanggal = $sampaiTanggal;
    }

    public function sheets(): array
    {
        $dataDetailInvoice = $this->ambilDataDetailInvoice();
        $dataRekapInvoice = $this->ambilDataRekapInvoice();

        return [
            new DetailInvoiceSheet($dataDetailInvoice),
            new RekapInvoiceSheet($dataRekapInvoice),
        ];
    }

    protected function ambilDataDetailInvoice()
    {
        return TransaksiObat::with('customer')
            ->whereHas('customer', function ($query) {
                $query->whereBetween('tanggal_registrasi', [
                    $this->dariTanggal . ' 00:00:00',
                    $this->sampaiTanggal . ' 23:59:59',
                ])
                    ->where('status', 'SELESAI');
            })
            ->get()
            ->sortBy(function ($item) {
                return $item->customer->metode_bayar . '_' . $item->customer->no_registrasi;
            })
            ->values();
    }

    protected function ambilDataRekapInvoice()
    {
        return Customer::with('transaksiObat')
            ->where('status', 'SELESAI')
            ->whereBetween('tanggal_registrasi', [
                $this->dariTanggal . ' 00:00:00',
                $this->sampaiTanggal . ' 23:59:59',
            ])
            ->get()
            ->sortBy(function ($item) {
                return $item->metode_bayar . '_' . $item->no_registrasi;
            })
            ->values();
    }
}
