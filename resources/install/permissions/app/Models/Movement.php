<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'touchdown',
        'onblocks',
        'offblocks',
        'airborne',
        'passengers',
        'remarks',
        'flight_id',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
