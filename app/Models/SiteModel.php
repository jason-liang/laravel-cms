<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteModel extends Model
{
  protected static function boot() {
    
    parent::boot();

    static::saved(function ($model) {

      clear_page_cache();

      return true;
    });

    static::deleted(function ($model) {
        
        clear_page_cache();
        
        return true;
    });
  }
}
