<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class citiesModel extends Model
{
    use HasFactory;
    protected $table = 'wp_cities';
    protected $primaryKey = 'id';

}
