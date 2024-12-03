<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\RoleHelper;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\User\RoleResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $roleHelper;

    public function __construct()
    {
        $this->roleHelper = new RoleHelper();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = ['name' => $request->name ?? ''];
        $sort = $request->sort ?? 'id DESC';
        $perPage = $request->per_page ?? 25;

        $roles = $this->roleHelper->getAll($filter, $perPage, $sort);

        return response()->success([
            'list' => RoleResource::collection($roles['data']),
            'meta' => [
                'total' => $roles['total']
            ],
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access']);

        $roleUser = $this->roleHelper->create($payload);

        if (!$roleUser['status']) {
            return response()->failed($roleUser['error'] ?? 'Gagal membuaat role user');
        }

        return response()->success(new RoleResource($roleUser['data']), 'Role user berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roleUser = $this->roleHelper->getById($id);

        if (!$roleUser['status'] || is_null('data')) {
            return response()->failed(['Data role user tidak ditemukan'], 404);
        }

        return response()->success(new RoleResource($roleUser['data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access', 'id']);

        $roleUser = $this->roleHelper->update($payload, $payload['id']);

        if (!$roleUser['status']) {
            return response()->failed($roleUser['error']);
        }

        return response()->success(new RoleResource($roleUser['data']), 'Role user berhasil diubah');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roleUser = $this->roleHelper->delete($id);

        if (!$roleUser) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($roleUser);
    }
}
