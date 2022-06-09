<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    use HasFactory;
    protected $fillable=[
        'registration_number',
        'status',
    ];
    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
