<?php

namespace App\Admin\Actions\Article;

use Encore\Admin\Actions\RowAction;

class ManageArticleInToptic extends RowAction
{
    public $name = '管理文章';

    public function href()
    {
        return admin_url("topics/{$this->row->id}");
    }

}