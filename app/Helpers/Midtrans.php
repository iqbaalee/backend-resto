<?php

namespace App\Helpers;

class Midtrans
{
    public static function pay($params)
    {
        \Midtrans\Config::$serverKey = "SB-Mid-server-ftymIGXtORd7uIPlZTtV36RQ";
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        return \Midtrans\Snap::createTransaction($params);
    }
}
