<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock', 'threshold'];

    // An ingredient belongs to many products
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('amount');
    }

    //     // Create an ingredient from an array of data
    // $ingredient = Ingredient::create(['name' => 'Beef', 'stock' => 20000, 'threshold' => 10000]);

    // // Update an ingredient from an array of data
    // $ingredient->update(['stock' => 15000]);
}
