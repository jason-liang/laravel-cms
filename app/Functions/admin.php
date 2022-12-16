<?php

use Encore\Admin\Widgets\Alert;
use Illuminate\Support\Facades\File;
use App\Models\Category;

if (! function_exists('alert_render')) {
  function alert_render ($msg = '', $title = '', $style = 'danger') {
    $alert = new Alert($msg, $title, $style);

    return $alert->render();
  }
  
}

if (! function_exists('alert_error_render')) {
  function alert_error_render ($msg = '', $title = '发生错误') {
    return alert_render($msg, $title, 'danger');
  }
}

if (! function_exists('alert_success_render')) {
  function alert_success_render ($msg = '', $title = '成功') {
    return alert_render($msg, $title, 'success');
  }
}

if (! function_exists('get_category_relationship')) {
  function get_category_relationship($current_id, &$relationship = '') {
    $current_cateogry = Category::find($current_id);
  
    if ($current_cateogry) {
      if ($current_cateogry->parent_id === 0) {
        $relationship = '|-' . $current_cateogry->name . $relationship ;
      } else {
        $relationship = '->' . $current_cateogry->name . $relationship ;
        get_category_relationship($current_cateogry->parent_id, $relationship);
      }
    }
  }
}

// 重新生成首页
if (! function_exists('generate_homepage_cache')) {
  function generate_homepage_cache() {

    $dirpath = storage_path('app/public/html');

    if (! File::isDirectory($dirpath)) {
      File::makeDirectory($dirpath, 0755, true, true);
    }

    $filepath = $dirpath . '/index.html';
    $view = view('site.home', []);

    if (File::exists($filepath)) {
      File::replace($filepath, $view);
    }

    File::put($filepath, $view);
  }
}

if (! function_exists('clear_page_cache')) {
  function clear_page_cache() {

    File::cleanDirectory(storage_path('app/public/html'));

    generate_homepage_cache();
  }
}


