<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'country',
        'contact_name',
        'contact_email',
        'contact_phone',
        'notes',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
