<?php

namespace App\Models\Revenues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Stores\Store;
use App\Models\Order\Order;

class Revenue extends Model
{
    use HasFactory;
   /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id','store_id','user_id','role_type','description','transaction_type','order_total_amount','order_sub_amount','order_discount_amount','order_delivery_amount','amount','current_balance','currency','status'
    ];

    /**
     * @return BelongsTo
     * @description get the detail associated with the user
     */
    public function user(): BelongsTo
    {    
        return $this->belongsTo(User::class,'user_id');
    }

     /**
     * @return BelongsTo
     * @description get the detail associated with the user
     */

    public function store(): BelongsTo
    {    
        return $this->belongsTo(Store::class);
    }

     /**
     * @return BelongsTo
     * @description get the detail associated with the user
     */

    public function order(): BelongsTo
    {    
        return $this->belongsTo(Order::class);
    }

    
}
