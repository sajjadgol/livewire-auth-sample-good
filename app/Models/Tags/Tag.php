<?php

namespace App\Models\Tags;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Tag extends Model implements TranslatableContract
{
    use HasFactory, Translatable;


    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $translatedAttributes = ['title'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'meta_title',
        'content',
        'order_number',
        'type',
        'status'
    ];
}
