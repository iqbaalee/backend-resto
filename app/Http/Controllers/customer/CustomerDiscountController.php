<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class CustomerDiscountController extends Controller
{
    public function getList()
    {
        try {
            $result = Discount::all();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id)
    {
        try {
            $menu = Discount::where('id', $id)->first();
            if(empty($menu)) {
                return response()->error(404, "Not Found!");
            }

            return response()->success($menu, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
