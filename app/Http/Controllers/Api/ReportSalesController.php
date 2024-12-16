<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Report\SalesCategoryHelper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportSalesController extends Controller
{
    private $salesCategoryHelper;

    public function __construct()
    {
        $this->salesCategoryHelper = new SalesCategoryHelper();
    }

    public function viewSalesCategories(Request $request)
    {
        $startDate     = $request->start_date ?? null;
        $endDate       = $request->end_date ?? null;
        $categoryId    = $request->category_id ?? null;
        $isExportExcel = $request->is_export_excel ?? null;

        $sales = $this->salesCategoryHelper->get($startDate, $endDate, $categoryId);

        if ($isExportExcel) {
            // dd($sales);
            return Excel::download(new ReportSalesCategory($sales), 'report-sales-category.xls');
        }
        return response()->success($sales['data'], '', [
            'dates'          => $sales['dates'] ?? [],
            'total_per_date' => $sales['total_per_date'] ?? [],
            'grand_total'    => $sales['grand_total'] ?? 0
        ]);
    }
}
