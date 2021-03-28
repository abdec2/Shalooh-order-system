<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpCountries extends Model
{
    use HasFactory;

    protected $table = 'wp_countries';

    public function WpCities()
    {
        return $this->hasMany(WpCities::class, 'country_id');
    } 

}
