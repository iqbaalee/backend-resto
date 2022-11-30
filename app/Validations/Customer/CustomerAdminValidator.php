<?php

namespace App\Validations\Customer;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerAdminValidator
{
    public static function validate($request, $rules)
    {

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}
