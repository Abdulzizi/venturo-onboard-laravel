<?php

namespace App\Helpers\User;

use App\Helpers\Venturo;
use App\Models\CustomerModel;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CustomerHelper extends Venturo
{
    const CUSTOMER_PHOTO_DIRECTORY = 'foto-customer';
    private $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    public function create(array $payload): array
    {
        try {
            $payload = $this->uploadGetPayload($payload);
            $customer = $this->customerModel->store($payload);


            return [
                'status' => true,
                'data' => $customer
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->customerModel->drop($id);


            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $customers = $this->customerModel->getAll($filter, $itemPerPage, $sort);


        return [
            'status' => true,
            'data' => $customers
        ];
    }

    public function getById(string $id): array
    {
        $customer = $this->customerModel->getById($id);
        if (empty($customer)) {
            return [
                'status' => false,
                'data' => null
            ];
        }


        return [
            'status' => true,
            'data' => $customer
        ];
    }

    public function update(array $payload, string $id): array
    {
        try {
            $payload = $this->uploadGetPayload($payload);
            $this->customerModel->edit($payload, $id);


            $customer = $this->getById($id);


            return [
                'status' => true,
                'data' => $customer['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function uploadGetPayload(array $payload)
    {
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'USER_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::CUSTOMER_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']);
        }

        return $payload;
    }
}
