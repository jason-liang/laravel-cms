<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayStatistic extends Model
{
    use HasFactory;

    protected $table = 'day_statistics';

    public $timestamps = false;

    protected $dateFormat = 'Y-m-d';

    protected $fillable = [ 'day', 'type', 'count' ];
}
