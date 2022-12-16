<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SiteModel;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostBox extends SiteModel
{
    use HasFactory, SoftDeletes, DefaultDatetimeFormat;

    protected $table = 'postboxes';
}
