<?php

namespace App\Http\Controllers\customer;

use App\Helpers\Midtrans;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Validations\Order\OrderValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function metaOrder()
    {
        return [
            "status" => [
                [
                    "key" => "initial",
                    "label" => "Menunggu Pembayaran",
                ],
                [
                    "key" => "down_payment",
                    "label" => "Menunggu Pelunasan",
                ],
                [
                    "key" => "paid",
                    "label" => "Lunas",
                ],
                [
                    "key" => "cancel",
                    "label" => "Dibatalkan",
                ],
            ],
        ];
    }

    //Order Active
    public function getList(Request $request)
    {
        try {
            $id = auth()
                ->guard("customer-api")
                ->user()->id;
            $result = Order::where([
                ["customer_id", "=", $id],
                ["order_date", ">=", Carbon::today()->toDateString("Y-m-d")],
                ["status", "LIKE", "%$request->status%"],
                ["order_number", "LIKE", "%$request->search%"],
            ])
                ->with([
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
                ])
                ->get();
            return response()->success(
                $result,
                200,
                "Success",
                $this->metaOrder()
            );
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function updateOrder($number)
    {
        try {
            $order = Order::where("order_number", $number)->first();
            $orderDetail = OrderDetail::where("order_id", $order->id)->get();
            $customer = Customer::where("id", $order->customer_id)->first();

            $productId = [];
            $products = [];
            foreach ($orderDetail as $value) {
                $products = $value;
                $productId[] = $value->product_id;
            }
            $product = Product::whereIn("id", $productId)->get();
            $price = 0;
            foreach ($product as $value) {
                $price += $value->price;
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
                    "gross_amount" => $price - (int) $order->down_payment,
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
                "down_payment" => (int) $order->down_payment,
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

    public function getHistory(Request $request)
    {
        try {
            $id = auth()
                ->guard("customer-api")
                ->user()->id;
            $result = Order::where([
                ["customer_id", "=", $id],
                ["order_date", "<", Carbon::today()->toDateString("Y-m-d")],
                ["status", "LIKE", "%$request->status%"],
                ["order_number", "LIKE", "%$request->search%"],
            ])
                ->with([
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
                ])
                ->get();
            return response()->success(
                $result,
                200,
                "Success",
                $this->metaOrder()
            );
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetail($number)
    {
        try {
            $id = auth()
                ->guard("customer-api")
                ->user()->id;

            $order = Order::where([
                ["customer_id", "=", $id],
                ["order_number", "=", $number],
            ])
                ->with([
                    "customer",
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
                ])
                ->first();
            $order->down_payment = (int) $order->down_payment;
            if (empty($order)) {
                return response()->error(404, "Not Found!");
            }

            return response()->success($order, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getDetailTransaction($number)
    {
        try {
            $order = Order::with([
                "customer",
                "order_detail" => function ($query) {
                    $query->with([
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
            ])
                ->where("order_number", $number)
                ->first();

            foreach ($order->order_detail as $v) {
                if (!empty($v->product)) {
                    $v->product->photo =
                        $v->product->photo != ""
                        ? asset("storage/meals/" . $v->product->photo)
                        : "";
                }
            }
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
        try {
            OrderValidator::validateOrderCustomer($request->all());
            $customer = auth()
                ->guard("customer-api")
                ->user();
            $id = $customer->id;
            $orderNumber = rand(1, 100) . date("YmdHis");
            $request["order_number"] = $orderNumber;
            $products = [];
            foreach ($request->products as $value) {
                $products[] = $value["id"];
            }

            $productsData = Product::whereIn("id", $products)->get();
            $productsWithQty = [];
            $productsMidtrans = [];
            $price = 0;
            foreach ($productsData as $key => $value) {
                $qty = $request->products[$key]["qty"];
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

            $midtrans = Midtrans::pay($params);

            $payloadOrder = [
                "order_number" => $orderNumber,
                "status" => "initial",
                "order_date" => $request->order_date,
                "customer_id" => $id,
                "down_payment" =>
                $request->payment_type == "paid" ? $price : $price / 2,
                "snap_token" => $midtrans->token,
                "redirect_url" => $midtrans->redirect_url,
            ];

            $order = Order::create($payloadOrder);
            for ($i = 0; $i < count($request->products); $i++) {
                OrderDetail::create([
                    "order_id" => $order->id,
                    "product_id" => $request->products[$i]["id"],
                    "qty" => $request->products[$i]["qty"],
                ]);
            }

            $response = [
                "order_id" => $order->id,
                "order_number" => $orderNumber,
                "status" => "initial",
                "order_date" => $request->order_date,
                "down_payment" =>
                $request->payment_type == "paid" ? $price : $price / 2,
                "total_billing" => $price,
                "products" => $productsWithQty,
                "customer" => $customer,
                "snap_token" => $midtrans->token,
                "redirect_url" => $midtrans->redirect_url,
            ];
            return response()->success($response, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getTotalOrder(Request $request)
    {
        try {
            $id = auth()
                ->guard("customer-api")
                ->user()->id;
            $resultHistory = Order::where([
                ["customer_id", "=", $id],
                ["order_date", "<", Carbon::today()->toDateString("Y-m-d")],
            ]);
            $resultActive = Order::where([
                ["customer_id", "=", $id],
                ["order_date", ">=", Carbon::today()->toDateString("Y-m-d")],
            ]);
            $result = [
                "history" => $resultHistory->count(),
                "active" => $resultActive->count(),
            ];
            return response()->success($result, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    public function getCurrentOrder(Request $request)
    {
        try {
            $id = auth()
                ->guard("customer-api")
                ->user()->id;
            $result = Order::where([
                ["customer_id", "=", $id],
                ["order_date", ">=", Carbon::today()->toDateString("Y-m-d")],
            ])
                ->with([
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
                ])
                ->get();
            return response()->success($result[0] ?? null, 200, "Success");
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
