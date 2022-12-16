<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;

class AdminUser extends Administrator 
{
  public function categories() {
    return $this->belongsToMany(Category::class, 'admin_user_categories', 'admin_user_id', 'category_id');
  }
}