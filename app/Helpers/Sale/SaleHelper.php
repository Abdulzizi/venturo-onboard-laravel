<?php

namespace App\Helpers\Sale;

use App\Helpers\Venturo;
use App\Models\SaleDetailModel;
use App\Models\SaleModel;
use Throwable;

class SaleHelper extends Venturo
{
    private $saleModel;
    private $saleDetailModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleDetailModel = new SaleDetailModel();
    }

    public function create(array $payload): array
    {
        try {
            $this->beginTransaction();

            // Create sale 
            $sale = $this->saleModel->create([
                'm_customer_id' => $payload['m_customer_id'],
                'date' => $payload['date'] ?? now(),
            ]);

            // Insert sale details
            $this->insertSaleDetails($payload['product_detail'], $sale->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sale,
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function update(array $payload): array
    {
        try {
            $this->beginTransaction();

            // Update sale
            $this->saleModel->findOrFail($payload['id'])->update([
                'm_customer_id' => $payload['m_customer_id'],
                'date' => isset($payload['date']) ? $payload['date'] : now(),
            ]);

            // Update sale details
            $this->updateSaleDetails($payload['product_detail'], $payload['id']);

            $sale = $this->saleModel->with('customer', 'saleDetails.product')->find($payload['id']);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sale
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function delete(string $id): array
    {
        try {
            $this->beginTransaction();

            // Delete sale details
            $this->saleDetailModel->where('t_sales_id', $id)->delete();

            // Delete sale
            $this->saleModel->find($id)->delete();

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $id,
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function getAll(array $filter = [], int $itemPerPage = 0, string $sort = '')
    {
        $sales = $this->saleModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $sales,
        ];
    }

    public function getById(string $id): array
    {
        $sale = $this->saleModel->with('saleDetails')->find($id);

        if (!$sale) {
            return [
                'status' => false,
                'data' => null,
            ];
        }

        return [
            'status' => true,
            'data' => $sale,
        ];
    }

    public function getSalesByCustomer(array $filter): array
    {
        $query = $this->saleModel->with(['customer', 'saleDetails.product']);

        // by customer
        if (!empty($filter['m_customer_id'])) {
            $query->where('m_customer_id', '=', $filter['m_customer_id']);
        }

        // date range filter
        if (!empty($filter['date_from']) && !empty($filter['date_to'])) {
            $query->whereBetween('date', [$filter['date_from'], $filter['date_to']]);
        }

        $sales = $query->get();

        $groupedSales = $sales->groupBy('customer.name')->map(function ($customerSales) {
            return [
                'customer_name' => $customerSales->first()->customer->name,
                'total_sales' => $customerSales->sum('saleDetails.price'),
                'transactions' => $customerSales,
            ];
        });

        return [
            'status' => true,
            'data' => $groupedSales->values(),
            'total_sales' => $groupedSales->sum('total_sales'),
        ];
    }


    // Private method
    private function insertSaleDetails(array $details, string $saleId): void
    {
        foreach ($details as $detail) {
            $this->saleDetailModel->create([
                't_sales_id' => $saleId,
                'm_product_id' => $detail['m_product_id'],
                'm_product_detail_id' => $detail['m_product_detail_id'],
                'total_item' => $detail['total_item'],
                'price' => $detail['price'],
            ]);
        }
    }

    private function updateSaleDetails(array $details, string $saleId): void
    {
        foreach ($details as $detail) {
            // Add new detail if "is_added" is set
            if (isset($detail['is_added']) && $detail['is_added']) {
                $detail['t_sales_id'] = $saleId;
                $this->saleDetailModel->create($detail);
                continue;
            }

            // Update existing detail if "is_updated" is set
            if (isset($detail['is_updated']) && $detail['is_updated']) {
                $saleDetail = $this->saleDetailModel->find($detail['id']);
                if ($saleDetail) {
                    $saleDetail->update($detail);
                }
            }
        }
    }
}
