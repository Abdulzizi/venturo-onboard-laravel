<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends Model implements CrudInterface
{
    use HasFactory, Uuid, SoftDeletes;

    protected $table = 'm_users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone_number',
        'm_user_roles_id', // foreign key
    ];

    public $timestamps = true;

    // Set the default role ID (jika tidak ada input)
    protected $attributes = [
        'm_user_roles_id' => "f9e49521-4a4a-4b3b-b0ca-73f36c8aef47", // Default role id
    ];

    // relasi one -> one ke m_user_roles
    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'm_user_roles_id', 'id');
    }

    // relasi one -> one ke m_customers
    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'm_user_id');
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['name'])) {
            $user->where('name', 'like', '%' . $filter['name'] . '%');
        }

        if (!empty($filter['email'])) {
            $user->where('email', 'LIKE', '%' . $filter['email'] . '%');
        }

        $sort = $sort ?: 'id DESC';
        $user->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $user->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function drop(string $id)
    {
        return $this->destroy($id);
    }
}
