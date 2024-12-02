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

    protected $table = 'm_user';
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone_number',
        'm_user_roles_id', //foreign key
    ];

    public $timestamps = true;

    // Set the default role ID if necessary (only when not specified)
    protected $attributes = [
        'm_user_roles_id' => 1, // Default role id
    ];

    // Relationship with RoleModel
    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'm_user_roles_id', 'id');
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
