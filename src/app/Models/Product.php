<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $id
 * @property mixed $rating
 * @property mixed $name
 * @property mixed $price
 * @property mixed $in_stock
 * @property Category $category
 */
class Product extends AbstractBaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'in_stock',
        'rating',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'in_stock' => 'boolean',
        'rating' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
