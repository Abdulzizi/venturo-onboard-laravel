<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use DateInterval;
use DatePeriod;
use DateTime;

use App\Models\SaleModel;

class SalesCategoryHelper extends Venturo
{
    protected $startDate;
    protected $endDate;
    protected $dates = [];
    protected $totalPerDate = [];
    protected $total = 0;
    protected $salesHelper;


    public function __construct()
    {
        $this->salesHelper = new SaleModel();
    }

    private function getPeriode()
    {
        // $begin = new DateTime($this->startDate);
        // $end = new DateTime($this->endDate);

        $begin = $this->startDate;
        $end = (clone $this->endDate)->modify('+1 day');

        // $end = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $dates = [];

        foreach ($period as $dt) {
            $date = $dt->format('Y-m-d');
            $dates[$date] = [
                'date_transaction' => $date,
                'total_sales' => 0,
            ];

            $this->setDefaultTotal($date);
            $this->setSelectedDate($date);
        }

        return $dates;
    }


    private function setDefaultTotal(string $date)
    {
        $this->totalPerDate[$date] = 0;
    }

    private function setSelectedDate(string $date)
    {
        $this->dates[] = $date;
    }

    private function reformatReport($list)
    {
        $list = $list->toArray();
        $periods = $this->getPeriode();
        $salesDetail = [];

        foreach ($list as $sales) {
            foreach ($sales['sale_details'] as $detail) {
                if (empty($detail['product'])) {
                    continue;
                }

                $date = date('Y-m-d', strtotime($sales['date']));
                $categoryId = $detail['product']['m_product_category_id'];
                $categoryName = $detail['product']['category']['name'] ?? 'Unknown';
                $productId = $detail['product']['id'];
                $productName = $detail['product']['name'];
                $totalSales = $detail['price'] * $detail['total_item'];

                $listTransactions = $salesDetail[$categoryId]['products'][$productId]['transactions'] ?? $periods;
                $subTotal = $salesDetail[$categoryId]['products'][$productId]['transactions'][$date]['total_sales'] ?? 0;
                $totalPerProduct = $salesDetail[$categoryId]['products'][$productId]['transactions_total'] ?? 0;
                $totalPerCategory = $salesDetail[$categoryId]['category_total'] ?? 0;

                $salesDetail[$categoryId] = [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName,
                    'category_total' => $totalPerCategory + $totalSales,
                    'products' => $salesDetail[$categoryId]['products'] ?? [],
                ];

                $salesDetail[$categoryId]['products'][$productId] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'transactions' => $listTransactions,
                    'transactions_total' => $totalPerProduct + $totalSales,
                ];

                $salesDetail[$categoryId]['products'][$productId]['transactions'][$date] = [
                    'date_transaction' => $date,
                    'total_sales' => $totalSales + $subTotal,
                ];

                $this->totalPerDate[$date] = ($this->totalPerDate[$date] ?? 0) + $totalSales;
                $this->total += $totalSales;
            }
        }

        return $this->convertNumericKey($salesDetail);
    }

    private function convertNumericKey($salesDetail)
    {
        $list = [];
        foreach ($salesDetail as $sales) {
            $category = [
                'category_id' => $sales['category_id'],
                'category_name' => $sales['category_name'],
                'category_total' => $sales['category_total'],
                'products' => [],
            ];

            foreach ($sales['products'] as $product) {
                $category['products'][] = [
                    'product_id' => $product['product_id'],
                    'product_name' => $product['product_name'],
                    'transactions' => array_values($product['transactions']),
                    'transactions_total' => $product['transactions_total'],
                ];
            }

            $list[] = $category;
        }

        return $list;
    }

    public function get($startDate, $endDate, $categoryId = '')
    {
        $this->startDate = new DateTime($startDate);
        $this->endDate = new DateTime($endDate);

        $sales = $this->salesHelper->getSalesByCategory($startDate, $endDate, $categoryId);

        return [
            'status' => true,
            'data' => $this->reformatReport($sales),
            'dates' => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total' => $this->total,
        ];
    }
}
