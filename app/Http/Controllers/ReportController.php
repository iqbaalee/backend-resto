<?php

namespace App\Http\Controllers;

use App\Http\Controllers\admin\OrderController;
use App\Models\Order;
use App\Validations\Report\ReportValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function mostBooking()
    {
        try {

            $mostBooking = Order::join('customers', 'orders.customer_id', '=', 'customers.id')->SelectRaw('COUNT(orders.customer_id) as total_booking, orders.customer_id, customers.name as customer_name')->groupBy('orders.customer_id', 'customers.name')->orderBy('total_booking', 'DESC')->limit(4)->get();
            return response()->success($mostBooking, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function chartOrder(Request $request)
    {
        try {
            $request->start_date = $request->start_date ?? date('Y-m-d');
            $request->end_date = $request->end_date ?? date('Y-m-d');

            $order = Order::whereBetween('order_date', [$request->start_date, $request->end_date])->select('order_date', DB::raw('count(id) as total_order'))->groupBy('order_date')->get();
            $chart = [];
            foreach ($order as $o) {
                $chart = [
                    'label' => [$o->order_date],
                    'data' => [$o->total_order]
                ];
            }
            return response()->success($chart, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function chartIncome(Request $request)
    {
        try {
            $request->start_date = $request->start_date ?? date('Y-m-d');
            $request->end_date = $request->end_date ?? date('Y-m-d');

            $order = Order::whereBetween('order_date', [$request->start_date, $request->end_date])->select('order_date', DB::raw('sum(down_payment) as total_income'))->groupBy('order_date')->get();
            $chart = [];
            foreach ($order as $o) {
                $chart['label'][] = $o->order_date;
                $chart['data'][] = $o->total_income;
            }
            return response()->success($chart, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function chartCustomer(Request $request)
    {
        try {
            $request->start_date = $request->start_date ?? date('Y-m-d');
            $request->end_date = $request->end_date ?? date('Y-m-d');

            $order = Order::whereBetween('order_date', [$request->start_date, $request->end_date])->select('order_date', DB::raw('count(customer_id) as total_customer'))->groupBy('order_date')->get();
            $chart = [];
            foreach ($order as $o) {
                $chart['label'][] = $o->order_date;
                $chart['data'][] = $o->total_customer;
            }
            return response()->success($chart, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
