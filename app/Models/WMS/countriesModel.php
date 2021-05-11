<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class countriesModel extends Model
{
    use HasFactory;
    protected $table = 'wp_countries';
    protected $primaryKey = 'id';

    

}
