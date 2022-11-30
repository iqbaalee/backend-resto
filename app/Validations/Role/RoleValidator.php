<?php

namespace App\Validations\Role;

use Illuminate\Support\Facades\Validator;

class RoleValidator
{
    public static function validate($request) {
        $rules = [
                'id'=> 'numeric',
                'name'=>'required|string',
                'permissions'=>'required|array',
                'permissions.*'=>'required|numeric',
            ];
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }

    public static function validatePermissions($request) {
        $rules = [
                'name'=> 'string',
                'permissions'=>'required|array',
                'permissions.*'=>'required|numeric',
            ];
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}