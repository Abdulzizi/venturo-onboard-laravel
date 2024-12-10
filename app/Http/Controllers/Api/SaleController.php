<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Sale\SaleHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    private $saleHelper;

    public function __construct()
    {
        $this->saleHelper = new SaleHelper();
    }
}
