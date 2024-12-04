<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Customer\CustomerHelper;
use App\Helpers\User\UserHelper;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
