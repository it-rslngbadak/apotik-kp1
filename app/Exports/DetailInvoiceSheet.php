<?php
// app/Exports/DetailInvoiceSheet.php
namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DetailInvoiceSheet implements FromArray, WithTitle, WithEvents
{
    protected $data;

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
        return 'Detail Invoice';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ==== JUDUL ====
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'LAPORAN DETAIL INVOICE');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE699');
                $sheet->getRowDimension(1)->setRowHeight(22);

                // ==== HEADER KOLOM ====
                $headers = [
                    'NO REG',
                    'TGL PELAYANAN',
                    'FARMALKES ID',
                    'NAMA OBAT',
                    'JUMLAH',
                    'HNA',
                    'HARGA JUAL (HARGA DIBAYAR PASIEN)',
                    'PPN',
                    'METODE BAYAR'
                ];
                foreach ($headers as $i => $header) {
                    $col = chr(65 + $i);
                    $sheet->setCellValue($col . '2', $header);
                }
                $sheet->getStyle('A2:I2')->getFont()->setBold(true);
                $sheet->getStyle('A2:I2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE699');
                $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal('center')->setWrapText(true);

                // ==== ISI DATA ====
                $row = 3;

                foreach ($this->data as $item) {
                    $sheet->setCellValue('A' . $row, $item->customer->no_registrasi);
                    $sheet->setCellValue('B' . $row, Carbon::parse($item->customer->tanggal_registrasi)->format('d/m/Y'));
                    $sheet->setCellValue('C' . $row, $item->farmalkes_id);
                    $sheet->setCellValue('D' . $row, $item->nama_obat);
                    $sheet->setCellValue('E' . $row, $item->jumlah);
                    $sheet->setCellValue('F' . $row, $item->hna);
                    $sheet->setCellValue('G' . $row, $item->sub_total);
                    $sheet->setCellValue('H' . $row, $item->ppn);
                    $sheet->setCellValue('I' . $row, $item->customer->metode_bayar);

                    $row++;
                }

                $lastDataRow = $row - 1;

                // ==== GRAND TOTAL PAKAI RUMUS SUM() ====
                if ($lastDataRow >= 3) {
                    $sheet->setCellValue('A' . $row, 'GRAND TOTAL');
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');

                    $sheet->setCellValue('G' . $row, "=SUM(G3:G{$lastDataRow})");
                    $sheet->setCellValue('H' . $row, "=SUM(H3:H{$lastDataRow})");
                } else {
                    $sheet->setCellValue('A' . $row, 'Tidak ada data pada rentang tanggal ini');
                    $sheet->mergeCells('A' . $row . ':I' . $row);
                }

                $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

                $sheet->getStyle('G3:H' . $row)->getNumberFormat()->setFormatCode('#,##0');

                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // BLOK TANDA TANGAN
                $baseRow = $row + 2; // kasih jarak 2 baris kosong dari GRAND TOTAL

                $sheet->setCellValue('G' . $baseRow, \Carbon\Carbon::now()->translatedFormat('l, d-m-Y'));
                $sheet->setCellValue('G' . ($baseRow + 1), 'Kasir Apotek');

                // jarak untuk ruang tanda tangan fisik (kosongkan beberapa baris)
                $rowNamaUser = $baseRow + 5;

                $sheet->setCellValue('G' . $rowNamaUser, auth()->user()->name ?? '-');
                $sheet->getStyle('G' . $rowNamaUser)->getFont()->setUnderline(true);

                $sheet->getStyle('G' . $baseRow . ':F' . ($baseRow + 1))->getFont()->setBold(false);
            },
        ];
    }
}
