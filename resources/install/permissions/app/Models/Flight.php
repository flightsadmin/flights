<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_id',
        'flight_no',
        'registration',
        'origin',
        'destination',
        'scheduled_time_arrival',
        'scheduled_time_departure',
        'flight_type',
        'linked_flight_id'
    ];

    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function movement()
    {
        return $this->hasMany(Movement::class);
    }

    public function linkedFlight()
    {
        return $this->belongsTo(Flight::class, 'linked_flight_id');
    }

    public function linkedFlights()
    {
        return $this->belongsTo(Flight::class, 'id');
    }
}