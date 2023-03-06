<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    use HasFactory;
    
    protected $table = 'airlines';

    protected $fillable = [
        'name', 
        'iata_code', 
        'base'
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
