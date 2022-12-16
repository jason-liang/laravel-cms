<?php

namespace App\Admin\Actions\Article;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
Use Encore\Admin\Admin;

class Check implements Renderable
{
  protected $model;

  public function __construct(Model $model)
  {
    $this->model = $model;
  }

  public function getCheckPath () {
    return "/admin/articles-no-check/{$this->model->id}";
  }

  public function getListPath () {
    return "/admin/articles-no-check";
  }

  public function render() {

    $trans = [
      'check_confirm' => '确定审核通过吗?',
      'confirm'       => trans('admin.confirm'),
      'cancel'        => trans('admin.cancel'),
      'check'         => '通过',
  ];

    $class = uniqid();

    $script = <<<SCRIPT

$('.{$class}-check').unbind('click').click(function() {

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
                    url: '{$this->getCheckPath()}',
                    data: {
                        status: 1,
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
    <a href="javascript:void(0);" class="btn btn-sm btn-success {$class}-check" title="{$trans['check']}">
      <i class="fa fa-check"></i>
      <span class="hidden-xs">
        {$trans['check']}
      </span>
    </a>
</div>
HTML;
  }
}