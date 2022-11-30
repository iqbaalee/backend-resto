<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Validations\Table\TableValidator;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $request->filter = $request->filter ?? date('Y-m-d');
            $checkFullDate = date('Y-m-d', strtotime($request->filter)) === $request->filter;
            $result = Product::where('type', 'table')->with(['orders' => function ($query) use ($request, $checkFullDate) {

                if ($checkFullDate) {
                    $query->join('customers', 'customers.id', '=', 'orders.customer_id')->select('orders.id', 'orders.order_date', 'orders.down_payment', 'customers.name', 'customers.phone', 'customers.email')->where('orders.order_date', '=', $request->filter);
                } else {
                    $query->join('customers', 'customers.id', '=', 'orders.customer_id')->select('orders.id', 'orders.order_date', 'orders.down_payment', 'customers.name', 'customers.phone', 'customers.email')->where('orders.order_date', 'like', '%' . $request->filter . '%');
                }
            }])->get();
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($id, Request $request)
    {
        try {
            if (!empty($request->order_id)) {
                $result =  Product::with(['orders' => function ($query) use ($request) {
                    $query->join('customers', 'customers.id', '=', 'orders.customer_id')->select('orders.id', 'orders.order_date', 'orders.down_payment', 'customers.name', 'customers.phone', 'customers.email')->where('orders.id', $request->order_id);
                }])->where('id', $id)->first();
            } else {

                $result = Product::with(['orders' => function ($query) {
                    $query->where('order_date', date('Y-m-d'));
                }])->where('id', $id)->first();
            }
            $result->orders[0]->down_payment = (int) $result->orders[0]->down_payment;

            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function addTable(Request $request)
    {
        try {
            $data = TableValidator::validate($request->all());
            $data['type'] = 'table';
            $result = Product::create($data);
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updateTable(Request $request, $id)
    {
        try {
            $data = TableValidator::validate($request->all());
            $product = Product::where('id', $id)->first();
            if (empty($product)) {
                return response()->error(404, "Not Found!");
            }
            Product::where('id', $id)->update($data);
            $data['id'] = (int)$id;
            return response()->success($data, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function deleteTable($id)
    {
        try {
            $product = Product::where('id', $id)->first();
            if (empty($product)) {
                return response()->error(404, "Not Found!");
            }
            Product::where('id', $id)->delete();
            return response()->success((int)$id, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
