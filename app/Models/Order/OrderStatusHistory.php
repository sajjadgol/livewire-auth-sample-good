<?php

namespace App\Models\Order;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatusHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'role','title', 'order_id', 'old_status', 'new_status'];

     /**
     * BelongsTo relation with store
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
