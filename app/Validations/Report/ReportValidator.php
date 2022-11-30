<?php

namespace App\Validations\Report;

use Illuminate\Support\Facades\Validator;

class ReportValidator
{
    public static function validate($request)
    {
        if (empty($request['start_date']) && empty($request['end_date']) || !empty($request['start_date']) && !empty($request['end_date'])) {
            return null;
        } else if (!empty($request['start_date']) && empty($request['end_date'])) {
            return "End date is required";
        } else if (empty($request['start_date']) && !empty($request['end_date'])) {
            return "Start date is required";
        }
    }
}
