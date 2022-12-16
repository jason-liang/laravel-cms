<?php

namespace App\Models;

use App\Models\SiteModel;

class OnDuty extends SiteModel
{
    protected $table = 'on_duties';

    protected $fillable = [ 'date', 'content' ];

    public function setContentAttribute($value) {
        $this->attributes['content'] = is_null($value) ? '' : $value;
    }
}
