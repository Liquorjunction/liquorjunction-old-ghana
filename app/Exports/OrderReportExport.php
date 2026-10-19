<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class OrderReportExport implements FromView, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data; // this is your $user_report with all order data
    }

    public function view(): View
    {
        return view('exports.order_report', [
            'orders' => $this->data,
        ]);
    }

    public function title(): string
    {
        return 'Order Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], 
        ];
    }
}