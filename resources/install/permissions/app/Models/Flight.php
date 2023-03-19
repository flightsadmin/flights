<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_no',
        'registration',
        'origin',
        'destination',
        'scheduled_time_arrival',
        'scheduled_time_departure',
        'flight_type'
    ];

    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function movement()
    {
        return $this->hasMany(Movement::class);
    }
}