<?php

namespace App\Validations\Auth;

use Illuminate\Support\Facades\Validator;

class LoginValidator
{
    public static function validate($request) {
        $rules = [
                'email'=>'required|string',
                'password'=> "required|string|min:3|max:12"
            ];
        
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }

    public static function validateUpdatePassword($request) {
        $rules = [
                'old_password'=>'required|string',
                'new_password'=> "required|string|min:3|max:12"
            ];
        
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}
