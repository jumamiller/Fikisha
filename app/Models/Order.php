<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        'order_number',
        'customer_id',
        'fleet_id',
        'description',
        'status'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}
