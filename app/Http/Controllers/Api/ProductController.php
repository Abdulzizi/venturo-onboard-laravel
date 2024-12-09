<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Customer\ProductHelper;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    private $productHelper;
    public function __construct()
    {
        $this->productHelper = new ProductHelper();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'm_product_category_id' => $request->product_category_id ?? '',
            'is_available' => isset($request->is_available) ? $request->is_available : '',
        ];
        $products = $this->productHelper->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new ProductCollection($products['data']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'name',
            'price',
            'description',
            'photo',
            'is_available',
            'details',
            'product_category_id'
        ]);

        $payload['m_product_category_id'] = $payload['product_category_id'];
        $product = $this->productHelper->create($payload);

        if (!$product['status']) {
            return response()->failed($product['error']);
        }

        return response()->success(new ProductResource($product['data']), 'product berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productHelper->getById($id);

        if (!($product['status'])) {
            return response()->failed(['Data product tidak ditemukan'], 404);
        }

        return response()->success(new ProductResource($product['data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'name',
            'price',
            'description',
            'photo',
            'is_available',
            'details',
            'details_deleted',
            'id',
            'product_category_id'
        ]);

        $payload['m_product_category_id'] = $payload['product_category_id'];

        // deleted code : $payload['id'] ?? 0
        $product = $this->productHelper->update($payload);

        if (!$product['status']) {
            return response()->failed($product['error']);
        }

        return response()->success(new ProductResource($product['data']), 'product berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->productHelper->delete($id);

        if (!$product['status']) {
            return response()->failed(['Mohon maaf product tidak ditemukan']);
        }

        return response()->success($product, 'product berhasil dihapus');
    }
}
