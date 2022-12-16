<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

use App\Models;

if (!function_exists('get_settings')) {

  function get_site_settings () {
    return Models\SiteBaseSetting::findOrFail(1);
  }
}

if (!function_exists('get_navbars')) {

  function get_navbars () {
    $model = new Models\SiteNavbar;

    return $model->toTree();
  }
}

if (!function_exists('get_friend_links')) {

  function get_friend_links () {
    $model = new Models\SiteFriendLink;

    return $model->toTree();
  }
}

if (!function_exists('get_latest_duty')) {

  function get_latest_duty () {
    $duty = Models\OnDuty::whereDate('date', '>=', Carbon::now())->orderBy('date', 'asc')->first();

    return $duty;
  }
}

if (!function_exists('get_latest_articles')) {

  function get_latest_articles ($limit = 10) {
    return Models\Article::where('status', 1)
                  ->orderBy('order', 'desc')
                  ->orderBy('updated_at', 'desc')
                  ->limit()
                  ->get();
  }
}

if (!function_exists('get_hot_articles')) {

  function get_hot_articles ($limit = 10) {
    return Models\Article::where('status', 1)
                  ->orderBy('is_hot', 'desc')
                  ->orderBy('order', 'desc')
                  ->orderBy('updated_at', 'desc')
                  ->limit()
                  ->get();
  }
}

if (!function_exists('get_sticky_articles')) {

  function get_sticky_articles ($limit = 10) {
    return Models\Article::where('status', 1)
                  ->orderBy('is_sticky', 'desc')
                  ->orderBy('order', 'desc')
                  ->orderBy('updated_at', 'desc')
                  ->limit()
                  ->get();
  }
}

if (!function_exists('get_recommend_articles')) {

  function get_recommend_articles ($limit = 10) {
    return Models\Article::where('status', 1)
                  ->orderBy('is_recommend', 'desc')
                  ->orderBy('order', 'desc')
                  ->orderBy('updated_at', 'desc')
                  ->limit()
                  ->get();
  }
}

if (!function_exists('get_articles_by_cagetory')) {

  function get_articles_by_cagetory ($cid, $limit = 10) {
    $children = Models\Category::findOrFail($cid)->children();

    if ($children->isEmpty()) {
      $query = Models\Article::where('category_id', $cid);
    } else {
      $childrenIds = $children->pluck('id');
      $query = Models\Article::whereIn('category_id', $childrenIds);
    }

    return $query->where('status', 1)
              ->orderBy('is_recommend', 'desc')
              ->orderBy('order', 'desc')
              ->orderBy('updated_at', 'desc')
              ->limit()
              ->get();
  }
}

