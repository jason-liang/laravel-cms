<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TopicArticle extends Pivot
{
    protected $table = 'topic_articles';

    protected static function boot() {
        parent::boot();
    
        static::created(function($model){

        });

        static::updated(function($model){

        });
    }
}
