<?php

namespace App\Validations\Order;

use Illuminate\Support\Facades\Validator;

class OrderValidator
{
    public static function validateAdminCreate($request)
    {
        $rules = [
            'customer_id' => 'nullable',
            'new_customer' => 'max:50',
            'table_id' => 'required|array',
            'payment' => 'integer',
            'order_date' => 'required|date',
            'meals' => 'required|array',
            'meals.*.product_id' => 'required',
            'meals.*.qty' => 'required|integer',
        ];
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }

    public static function validateOrderCustomer($request)
    {
        $rules = [
            "order_date" => "required|date",
            "products" => "required",
            "payment_type" => "required|string",
        ];
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}
