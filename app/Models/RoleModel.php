<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleModel extends Model implements CrudInterface
{
    use HasFactory, Uuid, SoftDeletes;

    protected $table = 'm_user_roles';
    protected $fillable = [
        'name',
        'access',
    ];

    public $timestamps = true;

    // Relationship with UserModel (one role has many users)
    public function users()
    {
        return $this->hasMany(UserModel::class, 'm_user_roles_id', 'id');
    }

    public function getAll(array $filter, int $itemPerPage = 25, string $sort = 'id DESC')
    {
        $userRoles = $this->query();

        if (!empty($filter['name'])) {
            $userRoles->where('name', 'like', '%' . $filter['name'] . '%');
        }

        // validasi sort format
        if (!preg_match('/^[a-zA-Z0-9_]+ (ASC|DESC)$/', $sort)) {
            $sort = 'id DESC';
        }

        $userRoles->orderByRaw($sort);

        return $userRoles->paginate($itemPerPage)->appends(['sort' => $sort]);
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
