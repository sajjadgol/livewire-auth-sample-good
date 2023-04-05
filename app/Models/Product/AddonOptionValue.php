<?php

namespace App\Models\Product;

use App\Models\Products\ProductAddons;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class AddonOptionValue extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_addon_option_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

      /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $translatedAttributes = ['name'];

    protected $fillable = [
        'id',
        'product_addon_option_id',
        'name',
        'price',
        'order_number',
        'meal_preparation_time',
    ];

    public function addonOption(): BelongsTo
    {
        return $this->belongsTo(AddonOption::class);
    }

    public function productAddons(): BelongsTo
    {
        return $this->belongsTo(ProductAddons::class);
    }
}
