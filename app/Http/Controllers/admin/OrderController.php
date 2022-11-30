<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Midtrans;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Validations\Order\OrderValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function getList(Request $request)
    {
        try {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $result = Order::with([
                    'customer',
                    "order_detail" => function ($query) {
                        $query
                            ->select("id", "order_id", "product_id", "qty")
                            ->with([
                                "product" => function ($query) {
                                    $query->select(
                                        "id",
                                        "name",
                                        "price",
                                        "type",
                                        "capacity",
                                        "photo"
                                    );
                                },
                            ]);
                    },
                ])->whereBetween('order_date', [$request->start_date, $request->end_date])->orderBy("order_date", "desc")->get();
            } else {
                $result = Order::with([
                    'customer',
                    "order_detail" => function ($query) {
                        $query
                            ->select("id", "order_id", "product_id", "qty")
                            ->with([
                                "product" => function ($query) {
                                    $query->select(
                                        "id",
                                        "name",
                                        "price",
                                        "type",
                                        "capacity",
                                        "photo"
                                    );
                                },
                            ]);
                    },
                ])->orderBy("order_date", "desc")->get();
            }

            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($number)
    {
        try {

            $order = Order::with(['customer', 'order_detail' => function ($query) {
                $query->with(['product' => function ($query) {
                    $query->select('id', 'name', 'price', 'type', 'capacity', 'photo');
                }]);
            }])->where("order_number", $number)->first();

            return response()->success($order, 200, "Success");

            if (empty($order)) {
                return response()->error(404, "Not Found!");
            }

            return response()->success($order, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function createOrder(Request $request)
    {
        DB::beginTransaction();
        try {


            if (
                empty($request->customer_id) &&
                !empty($request->new_customer)
            ) {
                $customerController = new CustomerController();
                $customer = $customerController->addCustomerFromOrder(
                    $request->new_customer
                );
                if ($customer != null) {
                    $cust_id = $customer["id"];
                }
                $request["customer_id"] = $cust_id;
            } else {
                $customerController = new CustomerController();
                $customer = $customerController->getDetailCustomer(
                    $request->customer_id
                );
            }
            OrderValidator::validateAdminCreate($request->all());

            $orderNumber = rand(1, 100) . date("YmdHis");
            $request["order_number"] = $orderNumber;

            $products = [];
            foreach ($request->meals as $value) {
                $products[] = $value["product_id"];
            }

            $productsData = Product::whereIn("id", $products)->get();
            $productsWithQty = [];
            $productsMidtrans = [];
            $price = 0;
            foreach ($productsData as $key => $value) {
                $qty = $request->meals[$key]["qty"];
                $value["qty"] = $qty;
                $value["total_price"] = ($value->price ?? 0) * $qty;
                $price += ($value->price ?? 0) * $qty;
                $productsWithQty[] = $value;
                $productsMidtrans[] = [
                    "id" => $value->id,
                    "price" => $value->price ?? 0,
                    "quantity" => $qty,
                    "name" => $value->name,
                ];
            }
            $customerMidtrans = [
                "first_name" => $customer["name"],
                "last_name" => "",
                "email" => $customer["email"],
                "phone" => $customer["phone"],
            ];
            $params = [
                "transaction_details" => [
                    "order_id" => $orderNumber,
                    "gross_amount" =>
                    $request->payment_type == "paid" ? $price : $price / 2,
                ],
                "customer_details" => $customerMidtrans,
                "item_details" => $productsMidtrans,
            ];
            // return $params;
            $midtrans = Midtrans::pay($params);
            $payloadOrder = [
                "order_number" => $orderNumber,
                "status" => "initial",
                "order_date" => $request->order_date,
                "customer_id" => $request->customer_id,
                "down_payment" =>
                $request->payment_type == "paid" ? $price : $price / 2,
                "snap_token" => $midtrans->token,
                "redirect_url" => $midtrans->redirect_url,
            ];

            $order = Order::create($payloadOrder);
            for ($i = 0; $i < count($request->meals); $i++) {
                OrderDetail::create([
                    "order_id" => $order->id,
                    "product_id" => $request->meals[$i]["product_id"],
                    "qty" => $request->meals[$i]["qty"],
                ]);
            }

            $productTable = [];
            for ($i = 0; $i < count($request->tables); $i++) {
                $productTable = [
                    "order_id" => $order->id,
                    "product_id" => $request->tables[$i]["product_id"],
                    "qty" => $request->tables[$i]["capacity"],
                ];
                OrderDetail::create($productTable);
            }
            DB::commit();
            $response = [
                "order_id" => $order->id,
                "order_number" => $orderNumber,
                "status" => "initial",
                "order_date" => $request->order_date,
                "down_payment" =>
                $request->payment_type == "paid" ? $price : $price / 2,
                "total_billing" => $price,
                "products" => [$productsWithQty, $productTable],
                "customer" => $customer,
                "snap_token" => $midtrans->token,
                "redirect_url" => $midtrans->redirect_url,
            ];

            return response()->success($response, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
            DB::rollBack();
        }
    }

    public function updateOrder($number)
    {
        try {
            $order = Order::where("order_number", $number)->first();
            $orderDetail = OrderDetail::with('product')->where("order_id", $order->id)->get();
            $customer = Customer::where("id", $order->customer_id)->first();

            $products = [];
            foreach ($orderDetail as $value) {
                $products = $value;
            }
            $price = 0;
            foreach ($orderDetail as $value) {
                if ($value->product->type == "table")  continue;
                $price += ($value->product->price * $value->qty);
            }
            $customerMidtrans = [
                "first_name" => $customer["name"],
                "last_name" => "",
                "email" => $customer["email"],
                "phone" => $customer["phone"],
            ];
            $params = [
                "transaction_details" => [
                    "order_id" => $number,
                    "gross_amount" =>
                    $price - (int)$order->down_payment,
                ],
                "customer_details" => $customerMidtrans,

            ];

            $midtrans = Midtrans::pay($params);

            Order::where("order_number", $number)->update([
                "status" => "paid",
                "down_payment" => $price,
                "snap_token" => $midtrans->token,
                "redirect_url" => $midtrans->redirect_url,
            ]);

            $response = [
                "order_id" => $order->id,
                "order_number" => $number,
                "status" => "initial",
                "order_date" => $order->order_date,
                "down_payment" => (int)$order->down_payment,
                "products" => $products,
                "total_billing" => $price,
                "customer" => $customer,
                "snap_token" => $midtrans->token,
                "redirect_url" => $midtrans->redirect_url,
            ];
            return response()->success($response, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getCount()
    {
        try {
            $order = Order::with(['order_detail' => function ($query) {
                $query->with(['product' => function ($query) {
                    $query->select('id')->where('type', 'meal');
                }]);
            }])->where('order_date', date('Y-m-d'))->select('customer_id', 'id')->get();
            $res = [];
            $productId = [];
            $customerId = [];
            foreach ($order as $o) {
                foreach ($o->order_detail as $val) {
                    if ($val->product == null) continue;
                    $productId[] = $val->product->id;
                }
                $customerId[] = $o->customer_id;
            }
            $res = [
                'order' => count($order),
                'customer' => count(array_unique($customerId)),
                'meal' => count(array_unique($productId))
            ];

            return response()->success($res, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
