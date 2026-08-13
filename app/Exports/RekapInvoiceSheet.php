<?php
// app/Exports/RekapInvoiceSheet.php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapInvoiceSheet implements FromArray, WithTitle, WithEvents
{
    protected $data; // collection of Customer

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Rekap Invoice';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ==== JUDUL ====
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'LAPORAN REKAP INVOICE');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

                // ==== HEADER KOLOM ====
                $sheet->setCellValue('A2', 'METODE BAYAR');
                $sheet->setCellValue('B2', 'NO REG');
                $sheet->setCellValue('C2', 'Sum of HARGA JUAL (HARGA DIBAYAR PASIEN)');
                $sheet->setCellValue('D2', 'Pembulatan (jika TUNAI)');
                $sheet->getStyle('A2:D2')->getFont()->setBold(true);
                $sheet->getStyle('A2:D2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
                $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center')->setWrapText(true);

                // ==== KELOMPOKKAN DI LEVEL COLLECTION, BUKAN QUERY ====
                $groupedByMetode = $this->data->groupBy('metode_bayar');

                $row = 3;

                foreach ($groupedByMetode as $metode => $customers) {
                    $metodeStartRow = $row;

                    foreach ($customers as $customer) {
                        $sheet->setCellValue('A' . $row, $metode);
                        $sheet->setCellValue('B' . $row, $customer->no_registrasi);

                        // pakai accessor total_biaya yang sudah otomatis membulatkan ke ratusan untuk TUNAI
                        $sheet->setCellValue('C' . $row, $customer->total_biaya_real);
                        if ($metode == 'TUNAI') {
                            $sheet->setCellValue('D' . $row, $customer->total_biaya);
                        }

                        $sheet->getRowDimension($row)->setOutlineLevel(1);
                        $row++;
                    }

                    $metodeEndRow = $row - 1;

                    // subtotal per metode bayar, tetap pakai rumus SUM() merujuk baris di sheet ini sendiri
                    $sheet->setCellValue('B' . $row, $metode . ' Total');
                    $sheet->setCellValue('C' . $row, "=SUM(C{$metodeStartRow}:C{$metodeEndRow})");
                    $sheet->setCellValue('D' . $row, "=SUM(D{$metodeStartRow}:D{$metodeEndRow})");
                    $sheet->getStyle('B' . $row . ':D' . $row)->getFont()->setBold(true);
                    $sheet->getStyle('B' . $row . ':D' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                    $row++;
                    // if ($metode == 'TUNAI') {
                    //     $sheet->setCellValue('B' . $row, $metode . ' Total Pembulatan');
                    //     $sheet->setCellValue('C' . $row, $customers->sum('total_biaya'));
                    //     $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
                    //     $sheet->getStyle('B' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                    //     $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    //     $row++;
                    // }
                }

                $sheet->setShowSummaryBelow(true);

                if ($row > 3) {
                    $sheet->getStyle('C3:C' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('D3:D' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
                }

                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
