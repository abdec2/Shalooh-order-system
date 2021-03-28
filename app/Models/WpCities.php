<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpCities extends Model
{
    use HasFactory;
    protected $table = 'wp_cities';


    public function WpCountries()
    {
        return $this->belongsTo(WpCountries::class, 'country_id');
    }
}
