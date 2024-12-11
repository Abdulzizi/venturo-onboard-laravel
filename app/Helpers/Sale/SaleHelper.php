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
                'date' => now(),
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
            $this->saleModel->find($payload['id'])->update([
                'm_customer_id' => $payload['m_customer_id'],
                'date' => now(),
            ]);

            // Update sale details
            $this->updateSaleDetails($payload['product_detail'], $payload['id']);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $this->getById($payload['id']),
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
        $sale = $this->saleModel->with('details')->find($id);

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
            // jika ada "is_added" maka buat detail baru
            if (isset($detail['is_added']) && $detail['is_added']) {
                $detail['t_sales_id'] = $saleId;
                $this->saleDetailModel->create($detail);
                continue;
            }

            // jika ada "is_updated" maka update detail
            if (isset($detail['is_updated']) && $detail['is_updated']) {
                $this->saleDetailModel->find($detail['id'])->update($detail);
            }
        }
    }
}