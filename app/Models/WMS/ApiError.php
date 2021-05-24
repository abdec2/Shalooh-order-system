<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiError extends Model
{
    use HasFactory;
    protected $table = 'api_error';
    protected $primaryKey = 'id';
}
