<?php

namespace App\Helpers\User;

use App\Helpers\Venturo;
use App\Models\RoleModel;
use Throwable;

class RoleHelper extends Venturo
{
    private $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel;
    }

    public function getAll(array $filter, int $itemPerPage = 25, string $sort = 'id DESC')
    {
        $roles = $this->roleModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $roles,
            'total' => $roles->total(),
        ];
    }


    public function getById(string $id): array
    {
        $role = $this->roleModel->getById($id);
        if (empty($role)) {
            return [
                'status' => false,
                'data' => null,
            ];
        }

        return [
            'status' => true,
            'data' => $role,
        ];
    }

    public function create(array $payload): array
    {
        try {

            $role = $this->roleModel->store($payload);

            return [
                'status' => true,
                'data' => $role,
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function update(array $payload, string $id): array
    {
        try {
            $updated = $this->roleModel->edit($payload, $id);

            if (!$updated) {
                throw new \Exception("Failed to update role.");
            }

            $role = $this->getById($id);

            if (!$role['status']) {
                throw new \Exception("Role not found after update.");
            }

            return [
                'status' => true,
                'data' => $role['data'],
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->roleModel->drop($id);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }
}
