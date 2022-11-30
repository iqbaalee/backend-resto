<?php

namespace App\Validations\Discount;

use Illuminate\Support\Facades\Validator;

class DiscountValidator
{
    public static function validate($request) {
        $rules = [
                'id'=> 'numeric',
                'name'=>'required|string',
                'description'=> "nullable|string",
                'min_order'=> "required|numeric"
            ];
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}