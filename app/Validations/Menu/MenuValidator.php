<?php

namespace App\Validations\Menu;

use Illuminate\Support\Facades\Validator;

class MenuValidator
{
    public static function validate($request) {
        $rules = [
                'id'=> 'numeric',
                'name'=>'required|string',
                'url'=> "required|string"
            ];
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}