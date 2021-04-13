<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $fillable = ['location', 'location_category_id', 'total_bins', 'bins_in_use', 'bin_init', 'bin_ending'];

    function Bins() {
        return $this->hasMany(Bins::class, 'location_id');
    }
}
