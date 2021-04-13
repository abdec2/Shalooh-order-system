<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\WMS\Bins;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = ['label', 'sku', 'parent', 'image_path'];


    function Bins() {
        return $this->hasMany(Bins::class, 'product_id');
    }

    
}
