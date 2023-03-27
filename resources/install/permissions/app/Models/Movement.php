<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'touchdown',
        'onblocks',
        'offblocks',
        'airborne',
        'passengers',
        'status',
        'remarks',
        'flight_id',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}