<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Specify the route key name for model binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Define relationships.
     */
    public function image()
    {
        return $this->hasOne(Productimage::class, 'product_id')->select('id', 'image', 'product_id');
    }

    public function images()
    {
        return $this->hasMany(Productimage::class, 'product_id')->select('id', 'image', 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->select('id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')->select('id', 'name', 'slug');
    }

    public function subcategory()
    {
        return $this->hasOne(Subcategory::class, 'id', 'subcategory_id')->select('id', 'subcategoryName', 'slug');
    }

    public function childcategory()
    {
        return $this->hasOne(Childcategory::class, 'id', 'childcategory_id')->select('id', 'childcategoryName', 'slug');
    }

    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id')->select('id', 'name', 'slug');
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'productsizes')->withTimestamps();
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'productcolors')->withTimestamps();
    }

    public function prosizes()
    {
        return $this->hasMany(Productsize::class);
    }

    public function procolors()
    {
        return $this->hasMany(Productcolor::class);
    }

    public function prosize()
    {
        return $this->hasOne(Productsize::class, 'product_id');
    }

    public function procolor()
    {
        return $this->hasOne(Productcolor::class, 'product_id');
    }

    /**
     * Get the SKU for the product.
     */
    public function getSkuAttribute()
    {
        return $this->attributes['sku'] ?? null;
    }

    /**
     * Set the SKU for the product.
     */
    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = strtoupper($value);
    }
    /**
     * Set the tags attribute as a comma-separated string.
     */
    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = is_array($value) ? implode(',', $value) : $value;
    }

    /**
     * Get the tags attribute as an array.
     */
    public function getTagsAttribute($value)
    {
        return explode(',', $value);
    }

    public function getSpecificationAttribute($value)
    {
        return $this->attributes['specification'] ?? null;
    }
    // Optionally, you can specify a cast for the specification field
    protected $casts = [
        'specification' => 'string',  // Convert the specification column to string
    ];

    // If you want to append 'specification' to the model's array form
    protected $appends = ['specification'];

}
