<?php

namespace App\Helpers\Customer;

use App\Helpers\Venturo;
use App\Models\ProductDetailModel;
use App\Models\ProductModel;
use Throwable;

class ProductHelper extends Venturo
{
    const PRODUCT_PHOTO_DIRECTORY = 'foto-produk';
    private $productModel;
    private $productDetailModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productDetailModel = new ProductDetailModel();
    }

    public function create(array $payload): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);

            $this->beginTransaction();

            $product = $this->productModel->store($payload);

            $this->insertUpdateDetail($payload['details'] ?? [], $product->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $product
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(int $productId)
    {
        try {
            $this->beginTransaction();

            $this->productModel->drop($productId);

            $this->productDetailModel->dropByProductId($productId);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $productId
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $categories = $this->productModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $categories
        ];
    }

    public function getById(string $id): array
    {
        $product = $this->productModel->getById($id);
        if (empty($product)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $product
        ];
    }

    public function update(array $payload): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);

            $this->beginTransaction();

            $this->productModel->edit($payload, $payload['id']);

            $this->insertUpdateDetail($payload['details'] ?? [], $payload['id']);
            $this->deleteDetail($payload['details_deleted'] ?? []);

            $product = $this->getById($payload['id']);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $product['data']
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    // Private method untuk delete detail product
    private function deleteDetail(array $details)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            $this->productDetailModel->drop($val['id']);
        }
    }

    // Private method untuk insert / update detail product
    private function insertUpdateDetail(array $details, int $productId)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            // Insert
            if (isset($val['is_added']) && $val['is_added']) {
                $val['m_product_id'] = $productId;
                $this->productDetailModel->store($val);
            }

            // Update
            if (isset($val['is_updated']) && $val['is_updated']) {
                $this->productDetailModel->edit($val, $val['id']);
            }
        }
    }

    private function uploadAndGetPayload(array $payload)
    {
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'PRODUCT_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::PRODUCT_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }
}
