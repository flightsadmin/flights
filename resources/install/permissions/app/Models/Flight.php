<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'flights';

    protected $fillable = [
        'flight_no',
        'registration',
        'origin',
        'destination',
        'scheduled_time_arrival',
        'scheduled_time_departure',
        'flight_type'
    ];
}
