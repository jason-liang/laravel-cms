<?php

namespace App\Admin\Libs\Tree;

use Illuminate\Contracts\Support\Renderable;
use App\Admin\Libs\Tree\Actions\RowAction;

class Actions implements Renderable
{
  public $path;

  public $keyName;

  public $branch;

  public $tree;

  protected $appends = [];

  protected $prepends = [];

  protected $actions = [
    'edit'     => Actions\Edit::class,
    'delete'   => Actions\Delete::class,
  ];

  protected $enableActions = [
    'edit'     => true,
    'delete'   => true
  ];

  public function __construct($path, $keyName, $branch, \App\Admin\Libs\Tree $tree)
  {
    $this->path = $path;

    $this->keyName = $keyName;

    $this->branch = $branch;

    $this->tree = $tree;
  }

  public function append($action)
  {
    $this->prepareAction($action);

    array_push($this->appends, $action);

    return $this;
  }

  public function prepend($action)
  {
    $this->prepareAction($action);

    array_unshift($this->prepends, $action);

    return $this;
  }

  protected function prepareAction($action)
  {
    if ($action instanceof RowAction) {
      $action->setActions($this);
    }
  }

  public function setEnableActions($enableActions) {
    $this->enableActions = $enableActions;
  }

  public function disableAllActions() {
    $enableActions = [];

    foreach ($this->enableActions as $action => $enable) {
      $enableActions[$action] = false;
    }

    $this->setEnableActions($enableActions);
  }

  public function render () {

    $renders = [];

    foreach ($this->prepends as $action) {

      if ($action instanceof RowAction) {
        $action = (new $action($this))->render();
      }

      $renders[] = $action;
    }

    foreach ($this->enableActions as $action => $enable) {

      if (! $enable) {
        continue;
      }

      $action = new $this->actions[$action]($this);
        
      $renders[] = $action->render();
    }

    foreach ($this->appends as $action) {

      if ($action instanceof RowAction) {
        $action = (new $action($this))->render();
      }

      $renders[] = $action;
    }

    return implode('', $renders);
  }

  public function __toString()
  {
    return $this->render();
  }
}
