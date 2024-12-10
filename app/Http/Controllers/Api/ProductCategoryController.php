<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Product\ProductCategoryHelper;
use App\Http\Resources\Product\CategoryCollection;
use App\Http\Requests\Product\CategoryRequest;
use App\Http\Resources\Product\CategoryResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    private $categoryHelper;

    public function __construct()
    {
        $this->categoryHelper = new ProductCategoryHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
        ];
        $categories = $this->categoryHelper->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new CategoryCollection($categories['data']));
    }

    public function show($id)
    {
        $category = $this->categoryHelper->getById($id);

        if (!($category['status'])) {
            return response()->failed(['Data category tidak ditemukan'], 404);
        }

        return response()->success(new CategoryResource($category['data']));
    }

    public function store(CategoryRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name']);
        $category = $this->categoryHelper->create($payload);

        if (!$category['status']) {
            return response()->failed($category['error']);
        }

        return response()->success(new CategoryResource($category['data']), 'category berhasil ditambahkan');
    }

    public function update(CategoryRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'id']);
        $category = $this->categoryHelper->update($payload, $payload['id'] ?? 0);

        if (!$category['status']) {
            return response()->failed($category['error']);
        }

        return response()->success(new CategoryResource($category['data']), 'category berhasil diubah');
    }

    public function destroy($id)
    {
        $category = $this->categoryHelper->delete($id);

        if (!$category) {
            return response()->failed(['Mohon maaf category tidak ditemukan']);
        }

        return response()->success($category, 'category berhasil dihapus');
    }
}
