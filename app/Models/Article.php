<?php

namespace App\Models;

use App\Models\SiteModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Support\Carbon;
use App\Models\DayStatistic;
use App\Models\MonthStatistic;
use App\Models\YearStatistic;

class Article extends SiteModel
{
    use SoftDeletes, DefaultDatetimeFormat;

    protected $table = "articles";

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->admin_user_id = Admin::user()->id;
        });

        static::created(function($model){
            $category = $model->category;

            if ($category) {
                $category->article_amounts += 1;

                $category->save();
            }

            $now = Carbon::now();
            // 日发布统计
            $dayStatistic = DayStatistic::firstOrNew([
                'day' => $now->format('Y-m-d'), 
                'type' => 'publish_articles'
            ]);
            $dayStatistic->count += 1;
            $dayStatistic->save();

            // 月发布统计
            $monthStatistic = MonthStatistic::whereYear('year_month', $now->format('Y'))
                                ->whereMonth('year_month', $now->format('m'))
                                ->first();
            if ($monthStatistic) {
                $monthStatistic->increment('count');
            } else {
                MonthStatistic::create([
                    'year_month' => $now->format('Y-m-d'),
                    'type' => 'publish_articles',
                    'count' => 1,
                ]);
            }
        
            // 年发布统计
            $yearStatistic = YearStatistic::firstOrNew([
                'year' => $now->format('Y'), 
                'type' => 'publish_articles'
            ]);
            $yearStatistic->count += 1;
            $yearStatistic->save();
        
        });
    
        static::deleted(function ($model) {
            $category = $model->category;

            if ($category) {
                $category->article_amounts -= 1;
                $category->save();
            }
            
            return true;
        });
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function author() {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public function topics() {
        return $this->belongsToMany(Topic::class, 'topic_articles', 'article_id', 'topic_id')
                    ->withPivot('order');
    }

    public function setImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['images'] = json_encode($value);
        }
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setContentAttribute($value) {
        $this->attributes['content'] = is_null($value) ? '' : $value;
    }

    public function setStatusAttribute($value) {
        $this->attributes['status'] = $value;
    }
}
