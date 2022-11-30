<?php

namespace App\Validations\Meal;

use Illuminate\Support\Facades\Validator;

class MealValidator
{
    public static function validate($request)
    {
        $rules = [
            'id' => 'numeric',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'photo' => 'nullable|string',
        ];
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}
