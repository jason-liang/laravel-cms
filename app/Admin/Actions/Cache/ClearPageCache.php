<?php

namespace App\Admin\Actions\Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
Use Encore\Admin\Admin;

class ClearPageCache implements Renderable
{
  protected $class;

  public function __construct()
  {
    $this->class = uniqid();
  }

  protected function getClearPath() {
    return '/admin/clear-page-cache';
  }

  protected function script() {

    $trans = [
      'check_confirm' => '确定清楚所有页面缓存吗?',
      'confirm'       => trans('admin.confirm'),
      'cancel'        => trans('admin.cancel')
    ];

    return <<<SCRIPT
      $('.{$this->class}-clear-page-cache').unbind('click').click(function() {

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
                url: '{$this->getClearPath()}',
                data: {
                  status: 1,
                  _method:'post',
                  _token:LA.token,
              },
                success: function (data) {
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
  }

  public function render() {

    Admin::script($this->script());

    return <<<EOD
    <li style="text-align: center;">
      <a class='{$this->class}-clear-page-cache' href="javascript:;">
        清除页面缓存
      </a>
    </li>
EOD;
  }
}