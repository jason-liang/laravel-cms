<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearStatistic extends Model
{
    use HasFactory;

    protected $table = 'year_statistics';

    public $timestamps = false;

    protected $fillable = [ 'year', 'type', 'count' ];

}
