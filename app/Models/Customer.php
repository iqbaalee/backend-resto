<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $softDelete = true;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
        'deleted_at'
    ];

    public function order()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }
}
