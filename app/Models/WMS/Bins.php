<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WMS\Locations;
use App\Models\WMS\Products;

class Bins extends Model
{
    use HasFactory;
    protected $table = 'bins';
    protected $primaryKey = 'id';

    function Locations() {
        return $this->belongsTo(Locations::class, 'location_id');
    }

    function Product() {
        return $this->belongsTo(Products::class, 'product_id');
    }

}
