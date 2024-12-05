<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Customer\CustomerHelper;
use App\Helpers\User\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\Customer\CustomerResource;

use Illuminate\Http\Request;
use DB;

class CustomerController extends Controller
{

    private $customer, $user;

    public function __construct()
    {
        $this->customer = new CustomerHelper();
        $this->user = new UserHelper();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
        ];
        $customers = $this->customer->getAll($filter, 5, $request->sort ?? '');

        return response()->success([
            'list' => CustomerResource::collection($customers['data']),
            'meta' => [
                'total' => $customers['total']
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        try {
            DB::beginTransaction();

            // Create user
            $payload_user = $request->only(['email', 'name', 'password']);
            $user = $this->user->create($payload_user);

            // Create customer
            $payload_customer = $request->only(['name', 'address', 'photo', 'phone']);
            $payload_customer['m_user_id'] = $user['data']->id; // linking user id
            $customer = $this->customer->create($payload_customer);

            if (!$customer['status']) {
                return response()->failed($customer['error']);
            }

            DB::commit();
            return response()->success(new CustomerResource($customer['data']), "Customer berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->failed($th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = $this->customer->getById($id);

        if (!($customer['status'])) {
            return response()->failed(['Data customer tidak ditemukan'], 404);
        }

        return response()->success(new CustomerResource($customer['data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }
        try {
            DB::beginTransaction();

            // Update user
            $payload_user = $request->only(['email', 'name', 'password']);
            $payload_user['id'] = $request->m_user_id; // User ID to update
            $user = $this->user->update($payload_user, $payload_user['id'] ?? 0);

            // Update customer
            $payload_customer = $request->only(['name', 'address', 'photo', 'id', 'phone']);
            $customer = $this->customer->update($payload_customer, $payload_customer['id'] ?? 0);

            if (!$customer['status']) {
                return response()->failed($customer['error']);
            }

            DB::commit();
            return response()->success(new CustomerResource($customer['data']), "Customer berhasil diubah");
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->failed($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = $this->customer->delete($id);

        if (!$customer) {
            return response()->failed(['Mohon maaf data customer tidak ditemukan']);
        }

        return response()->success($customer, "Customer berhasil dihapus");
    }
}
