<?php

namespace App\Admin\Libs\Tree\Actions;

use App\Admin\Libs\Tree\Actions;

class RowAction
{
  protected $actions;

  public function __construct(Actions $actions = null)
  {
    $this->actions = $actions;
  }

  public function setActions(Actions $actions) {
    $this->actions = $actions;
  }
}
