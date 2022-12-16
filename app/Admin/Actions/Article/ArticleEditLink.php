<?php

namespace App\Admin\Actions\Article;

use Encore\Admin\Actions\RowAction;

class ArticleEditLink extends RowAction
{
    public $name = '编辑';

    public function href()
    {
        return admin_url("articles/{$this->row->id}");
    }

}