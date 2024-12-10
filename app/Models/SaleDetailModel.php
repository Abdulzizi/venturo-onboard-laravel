<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Models\ProductDetailModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetailModel extends Model implements CrudInterface
{
    use HasFactory, Uuid, SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        't_sales_id', // dari t_sales
        'm_product_id', // dari m_products
        'm_product_detail_id', // dari m_product_details
        'total_item',
        'price',
    ];

    protected $table = 't_sales_details';

    // Relasi many -> one ke t_sales
    public function sale()
    {
        return $this->belongsTo(SaleModel::class, 't_sales_id');
    }

    // Relasi many -> one ke m_product
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'm_product_id');
    }

    // Relasi many -> one ke m_product_detail
    public function productDetail()
    {
        return $this->belongsTo(ProductDetailModel::class, 'm_product_detail_id');
    }

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

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $query = $this->query();

        // Filtering
        // by sale id
        if (!empty($filter['t_sales_id'])) {
            $query->where('t_sales_id', '=', $filter['t_sales_id']);
        }

        // by product id
        if (!empty($filter['m_product_id'])) {
            $query->where('m_product_id', '=', $filter['m_product_id']);
        }

        // by detail product id
        if (!empty($filter['m_product_detail_id'])) {
            $query->where('m_product_detail_id', '=', $filter['m_product_detail_id']);
        }

        // by price
        if (!empty($filter['price'])) {
            $query->where('price', '=', $filter['price']);
        }

        // by total item sold
        if (!empty($filter['total_item'])) {
            $query->where('total_item', '=', $filter['total_item']);
        }

        $sort = $sort ?: 'id DESC';
        $query->orderByRaw($sort);

        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $query->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }
}
