<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numeric_code', 
        'alpha_numeric_code', 
        'description',
        'accountable',
        'airline_id'
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }
}
