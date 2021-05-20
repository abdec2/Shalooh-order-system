<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronJobModel extends Model
{
    use HasFactory;
    protected $table = 'cron_job';
    protected $primaryKey = 'id';

    
}
