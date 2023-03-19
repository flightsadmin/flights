<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type',
        'start',
        'finish',
        'flight_id',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}