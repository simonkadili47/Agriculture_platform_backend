<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'farmer_id',
        'category_id',
        'product_name',
        'selling_price',
        'quantity',
        'product_description',
        'product_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }
}
