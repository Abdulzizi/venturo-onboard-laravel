<?php

namespace App\Models;

use App\Repository\CrudInterface;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Uuid;
use Illuminate\Foundation\Auth\User;

class CustomerModel extends Model implements CrudInterface
{
    use HasFactory, SoftDeletes, Uuid;

    public $timestamps = true;

    protected $fillable = [
        'id',
        'm_user_id',
        'name',
        'address',
        'photo',
        'phone',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $table = 'm_customers';

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $customer = $this->query();

        if (!empty($filter['name'])) {
            $customer->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        $sort = $sort ?: 'id DESC';
        $customer->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $customer->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'm_user_id');
    }
}
