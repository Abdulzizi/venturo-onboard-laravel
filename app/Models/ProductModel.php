<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model implements CrudInterface
{
    use HasFactory, SoftDeletes, Uuid;

    public $timestamps = true;

    protected $fillable = [
        'm_product_category_id',
        'name',
        'price',
        'description',
        'photo',
        'is_available'
    ];

    protected $table = 'm_products';

    // Relasi many -> one ke m_product_category
    public function category()
    {
        return $this->belongsTo(ProductCategoryModel::class, 'm_product_category_id');
    }

    // Relasi one -> many ke m_product_detail
    public function details()
    {
        return $this->hasMany(ProductDetailModel::class, 'm_product_id', 'id');
    }

    // CRUD interface
    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    // Get list product & Get spesifik product
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['name'])) {
            $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if (!empty($filter['m_product_category_id'])) {
            $user->where('m_product_category_id', '=', $filter['m_product_category_id']);
        }

        if ($filter['is_available'] != '') {
            $user->where('is_available', '=', $filter['is_available']);
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
}
