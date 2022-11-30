<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function payment_handler(Request $request)
    {
        $json = json_decode($request->getContent());
        $signature_key = hash(
            "sha512",
            $json->order_id .
                $json->status_code .
                $json->gross_amount .
                "SB-Mid-server-ftymIGXtORd7uIPlZTtV36RQ"
        );

        if ($signature_key != $json->signature_key) {
            return response()->error(404, "Invalid signature key!");
        }
        //change status
        $order = Order::where("order_number", $json->order_id)
            ->with([
                "order_detail" => function ($query) {
                    $query
                        ->select("id", "order_id", "product_id", "qty")
                        ->with([
                            "product" => function ($query) {
                                $query->select("id", "price");
                            },
                        ]);
                },
            ])
            ->first();
        $total_price = 0;
        foreach ($order->order_detail as $value) {
            $total_price += $value->qty * $value->product->price;
        }
        switch ($json->transaction_status) {
            case "capture":
                return $order->update(["status" => "initial"]);
            case "pending":
                return $order->update(["status" => "initial"]);
            case "settlement":
                $status =
                    $order->down_payment < $total_price
                    ? "down_payment"
                    : "paid";
                //kurangi stock jika dp > total price
                return $order->update(["status" => $status]);
            default:
                return $order->update(["status" => "cancel"]);
        }
    }
}
