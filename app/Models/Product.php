<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit_of_measure',
        'description',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
