<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthStatistic extends Model
{
    use HasFactory;

    protected $table = 'month_statistics';

    public $timestamps = false;

    protected $dateFormat = 'Y-m';

    protected $fillable = [ 'year_month', 'type', 'count' ];
}
