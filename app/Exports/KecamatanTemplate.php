<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KecamatanTemplate implements FromArray, WithHeadings, WithEvents, ShouldAutoSize {
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $count_data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->count_data = count($data);
    }

    public function array(): array
    {   
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'KODE_KABUPATEN',
            'KABUPATEN',
            'KODE_KECAMATAN',
            'KECAMATAN'
        ];
    }

    public function registerEvents(): array
    {
        $lastcell = $this->count_data + 1;

        return [
            AfterSheet::class => function(AfterSheet $event) use($lastcell) {
                $headerRange = 'A1:D1';
                // $fullCellRange = 'A1:AA'.$lastcell;

                // set border
                $styleBorder = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['rgb' => '000000'],
                        ]
                    ]
                ];
                // $event->sheet->getDelegate()->getStyle($fullCellRange)->applyFromArray($styleBorder);
                $event->sheet->getDelegate()->getStyle($headerRange)->applyFromArray($styleBorder);
                
                // set font & bgcolor
                $styleHeaders = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'C0C0C0']
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10
                    ]
                ];
                $event->sheet->getDelegate()->getStyle($headerRange)->applyFromArray($styleHeaders);
            },
        ];
    }
}