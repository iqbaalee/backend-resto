<?php

namespace App\Validations\Table;

use Illuminate\Support\Facades\Validator;

class TableValidator
{
    public static function validate($request) {
        $rules = [
                'id'=> 'numeric',
                'name'=>'required|string',
                'description'=>'nullable|string',
                'capacity'=> 'required|numeric',
            ];
        $validator = Validator::make($request, $rules);
        if($validator->fails()) {
            throw new \Exception($validator->messages()->first(), 400);
        }
        return $validator->validate();
    }
}