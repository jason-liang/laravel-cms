<?php

namespace App\Admin\Libs\Tree\Actions;

use Illuminate\Contracts\Support\Renderable;

class Edit extends RowAction implements Renderable
{

  public function render() {

    $url = url("{$this->actions->path}/{$this->actions->branch[$this->actions->keyName]}/edit");
    
    return <<<EOD
      <a href="{$url}" style="margin-right: 5px;">
        <i class="fa fa-edit fa-lg" style="vertical-align: text-bottom;"></i>
      </a>
EOD;
  }
}
