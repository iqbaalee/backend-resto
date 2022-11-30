<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CustomerMealController extends Controller
{
    public function getList()
    {
        try {
            $result = Product::where('stock', '>', 0)->where('type', 'meal')->get();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $result =  Product::where('id', $id)->where('type', 'meal')->first();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
