<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableStocks extends Model
{
    use HasFactory;
    protected $table = 'available_stock';
    protected $primaryKey = 'id';


    protected $fillable = ['product_id', 'available_qty'];

}
