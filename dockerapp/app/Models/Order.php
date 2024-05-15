<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = "STATUS_PENDING";
    const STATUS_APPROVED = "STATUS_APPROVED";
    const STATUS_REJECTED = "STATUS_REJECTED";
    const STATUS_STARTED = "STATUS_STARTED";
    const STATUS_FINISHED = "STATUS_FINISHED";

    protected $table = 'orders';
    protected $fillable = ['name', 'payment_path'];

    public function products(){
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
