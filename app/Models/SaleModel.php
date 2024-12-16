<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Models\CustomerModel;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleModel extends Model implements CrudInterface
{
    use HasFactory, SoftDeletes, Uuid;

    public $timestamps = true;

    protected $fillable = [
        'm_customer_id',  // customer id dari m_customers
        'date',
    ];

    protected $table = 't_sales';

    // Relasi many -> one ke m_customer
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'm_customer_id');
    }

    // Relasi one -> many ke t_sales_detail
    public function saleDetails()
    {
        return $this->hasMany(SaleDetailModel::class, 't_sales_id');
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

        if (!empty($filter['m_customer_id'])) {
            $query->where('m_customer_id', '=', $filter['m_customer_id']);
        }

        if (!empty($filter['date'])) {
            $query->where('date', 'LIKE', '%' . $filter['date'] . '%');
        }

        $sort = $sort ?: 'date DESC';
        $query->orderByRaw($sort);

        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $query->paginate($itemPerPage)->appends('sort', $sort);
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    // Untuk get by category dan periode
    public function getSalesByCategory($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'saleDetails.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            // 'saleDetails',
            'saleDetails.product.category'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales = $sales->whereBetween('date', [
                $startDate . ' 00:00:01',
                $endDate . ' 23:59:59',
            ]);
        }

        return $sales->orderByDesc('date')->limit(2)->get();
    }
}
