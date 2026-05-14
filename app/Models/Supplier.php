<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'country', 'contact_email', 'contact_phone', 'notes'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
