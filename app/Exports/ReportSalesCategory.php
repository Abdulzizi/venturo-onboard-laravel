<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportSalesCategory implements FromView
{
    private $reports;

    public function __construct(array $sales)
    {
        $this->reports = $sales;
    }

    public function view(): View
    {
        // dd($this->reports);
        return view('generate.excel.report-sales', $this->reports);
    }
}
