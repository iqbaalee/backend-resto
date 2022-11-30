<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'menu_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function menu() {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }
}
