<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WMS\Locations;
use App\Models\WMS\Products;
use App\Models\WMS\Inventory;

class Bins extends Model
{
    use HasFactory;
    protected $table = 'bins';
    protected $primaryKey = 'id';


    protected $fillable = ['bin_location', 'location_id', 'product_id', 'tag_number'];


    function Locations() {
        return $this->belongsTo(Locations::class, 'location_id');
    }

    function Product() {
        return $this->belongsTo(Products::class, 'product_id');
    }
    
    function Inventory()
    {
        return $this->hasMany(Inventory::class, 'bin_id');
    }

}
