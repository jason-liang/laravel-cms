<?php

namespace App\Admin\Actions\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
Use Encore\Admin\Admin;

class UnCheck implements Renderable
{
  protected $model;

  public function __construct(Model $model)
  {
    $this->model = $model;
  }

  public function getUnCheckPath () {
    return "/admin/articles-no-check/{$this->model->id}";
  }

  public function getListPath () {
    return "/admin/articles-no-check";
  }

  public function render() {

    $trans = [
      'check_confirm' => '确定拒绝通过吗?',
      'confirm'       => trans('admin.confirm'),
      'cancel'        => trans('admin.cancel'),
      'uncheck'         => '拒绝',
  ];

    $class = uniqid();

    $script = <<<SCRIPT

$('.{$class}-uncheck').unbind('click').click(function() {

    swal({
        title: "{$trans['check_confirm']}",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "{$trans['confirm']}",
        showLoaderOnConfirm: true,
        cancelButtonText: "{$trans['cancel']}",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '{$this->getUnCheckPath()}',
                    data: {
                        status: -1,
                        _method:'put',
                        _token:LA.token,
                    },
                    success: function (data) {
                        $.pjax({container:'#pjax-container', url: '{$this->getListPath()}' });

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

SCRIPT;
        Admin::script($script);

        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="javascript:void(0);" class="btn btn-sm btn-danger {$class}-uncheck" title="{$trans['uncheck']}">
      <i class="fa fa-close"></i>
      <span class="hidden-xs">
        {$trans['uncheck']}
      </span>
    </a>
</div>
HTML;
  }
}