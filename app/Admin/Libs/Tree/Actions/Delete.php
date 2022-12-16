<?php

namespace App\Admin\Libs\Tree\Actions;

use Illuminate\Contracts\Support\Renderable;

class Delete extends RowAction implements Renderable
{
  public function render() {

    return <<<EOD
     <a 
        href="javascript:void(0);" 
        data-id="{$this->actions->branch[$this->actions->keyName]}" 
        class="tree_branch_delete text-danger" 
        style="vertical-align: middle;"
      >
        <i class="fa fa-trash fa-lg"></i>
      </a>
EOD;
  }
}
