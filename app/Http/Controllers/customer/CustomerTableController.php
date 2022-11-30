<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerTableController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $result = Product::where('type', 'table')->with(['orders' => function ($query) use ($request) {
                $date = Carbon::today()->toDateString('Y-m-d');
                if (!empty($request->filter)) {
                    $date = $request->filter;
                }
                $query->join('customers', 'customers.id', '=', 'orders.customer_id')->select('orders.id', 'orders.order_date', 'orders.down_payment', 'customers.name', 'customers.phone', 'customers.email')->where('orders.order_date', $date);
            }])->get();

            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id, Request $request)
    {
        try {
            if (!$request->order_id) {
                return response()->error(400, "Order id is required");
            }
            $result = Order::select('id', 'order_number', 'customer_id', 'order_number', 'status', 'down_payment')->with(['customer', 'order_detail' => function ($query) use ($id, $request) {
                $query->where('product_id', $request->product_id)->with('product')->select('id', 'order_id', 'product_id');
            }])->where(['orders.id' => $request->order_id])->first();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
