<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use App\Models\SiteModel;

class Topic extends SiteModel
{
    use HasFactory, SoftDeletes, DefaultDatetimeFormat;

    protected $table = 'topics';

    protected static function boot() {
      parent::boot();
  
      static::deleting(function ($model) {

        if ($model->articles()->exists()) {
          throw new \Exception('该专题下还有文章，无法删除！');
        }

        return true;
      });
  }

  public function articles() {
    return $this->belongsToMany(Article::class, 'topic_articles', 'topic_id', 'article_id')
                ->withPivot('order')
                ->orderBy('topic_articles.order', 'desc');
  }
}
