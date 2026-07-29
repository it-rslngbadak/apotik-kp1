<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
        ]);

        $dariTanggal = $request->dari_tanggal;
        $sampaiTanggal = $request->sampai_tanggal;

        $filename = 'laporan-invoice_' . $dariTanggal . '_sd_' . $sampaiTanggal . '.xlsx';

        return Excel::download(
            new InvoiceReportExport($dariTanggal, $sampaiTanggal),
            $filename
        );
    }
}
