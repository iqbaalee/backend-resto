<?php

namespace App\Validations\Auth;

use Illuminate\Support\Facades\Validator;

class RegisterValidator
{
    public static function validate($request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => "required|string|min:3|max:12",
            'role_id' => "required|numeric"
        ];

        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }

    public static function validateUpdate($request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string',
            'role_id' => "required|numeric"
        ];

        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }

    public static function validateCustomer($request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:customers',
            'phone' => 'string',
            'address' => 'string',
            'password' => "required|string|min:3|max:12",
        ];

        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }

    public static function validateUpdateCustomer($request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:customers,email,' . $request['id'],
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ];

        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}
