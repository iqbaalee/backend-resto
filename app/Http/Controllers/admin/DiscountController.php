<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Validations\Discount\DiscountValidator;
use Illuminate\Http\Request;

class DiscountController extends Controller
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

    public function addOrUpdate(Request $request)
    {
        try {
            $data = DiscountValidator::validate($request->all());
            Discount::upsert(
                $data,
                ['id'],
                ['name', 'description', 'min_order']
            );
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteDiscount($id)
    {
        try {
            $menu = Discount::where('id', $id)->first();
            if(empty($menu)) {
                return response()->error(404, "Not Found!");
            }

            Discount::where('id', $id)->delete();

            return response()->success($id, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
