<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "order_number",
        "status",
        "order_date",
        "customer_id",
        "down_payment",
        "snap_token",
    ];
    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class, "order_id", "id");
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, "id", "customer_id");
    }
}
