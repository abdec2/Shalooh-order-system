<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldStock extends Model
{
    use HasFactory;
    protected $table = 'hold_stock';
    protected $primaryKey = 'id';
}
