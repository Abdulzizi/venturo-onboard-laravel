<?php

namespace App\Exports;

use App\Models\SaleModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
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
        return view('generate.excel.report-sales', $this->reports);
    }
}
