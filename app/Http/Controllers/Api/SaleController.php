<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sale\SaleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\SaleRequest;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleResource;

use Illuminate\Http\Request;

class SaleController extends Controller
{
    private $saleHelper;

    public function __construct()
    {
        $this->saleHelper = new SaleHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'm_customer_id' => !empty($request->customer_id) ? explode(",", $request->customer_id) : [], // by customer
            'm_product_id' => !empty($request->menu_id) ? explode(",", $request->menu_id) : [], // by menu
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
        ];

        // dd($filter);

        $sales = $this->saleHelper->getAll($filter, (int)($request->per_page ?? 25), $request->sort ?? '');

        return response()->success(new SaleCollection($sales['data']), '', [
            'filters' => $filter
        ]);
    }

    public function store(SaleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'm_customer_id',
            'date',
            'product_detail',
        ]);

        $sale = $this->saleHelper->create($payload);

        if (!$sale['status']) {
            return response()->failed($sale['error']);
        }

        return response()->success(new SaleResource($sale['data']), 'Sale berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $sale = $this->saleHelper->getById($id);

        if (!($sale['status'])) {
            return response()->failed(['Data sale tidak ditemukan'], 404);
        }

        return response()->success(new SaleResource($sale['data']));
    }

    public function update(SaleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'm_customer_id',
            'date',
            'product_detail',
            'details_deleted',
        ]);

        $sale = $this->saleHelper->update($payload);

        if (!$sale['status']) {
            return response()->failed($sale['error']);
        }

        return response()->success(new SaleResource($sale['data']), 'Sale berhasil diubah');
    }

    public function destroy(string $id)
    {
        $sale = $this->saleHelper->delete($id);

        if (!$sale['status']) {
            return response()->failed(['Mohon maaf sale tidak ditemukan']);
        }

        return response()->success($sale, 'Sale berhasil dihapus');
    }

    public function getSalesByCustomer(Request $request)
    {
        $filter = [
            'm_customer_id' => $request->customer_id ?? '',
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
        ];

        $sales = $this->saleHelper->getSalesByCustomer($filter);

        return response()->success($sales['data'], '', [
            'filters' => $filter,
            'total_sales' => $sales['total_sales'],
        ]);
    }
}
