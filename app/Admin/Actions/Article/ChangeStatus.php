<?php

namespace App\Admin\Actions\Article;

use Encore\Admin\Actions\RowAction;

class ChangeStatus extends RowAction
{
    public $name = '审核';

    public function href()
    {
        return admin_url("articles-no-check/{$this->row->id}");
    }

}