<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class DutyImportTool extends AbstractTool 
{
  protected function script() {
    
  }

  public function render() {
    
    Admin::script($this->script());

    return '<button class="btn btn-primary">导入</button>';
  }
}