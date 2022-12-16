<?php

namespace App\Models;

use App\Models\SiteModel;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Category extends SiteModel
{
    use SoftDeletes, ModelTree {
        ModelTree::boot as modelTreeBoot;
    }

    protected $table = "categories";

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        $this->setTitleColumn('name');
    }

    public function articles() {
        return $this->hasMany(Article::class);
    }

}
