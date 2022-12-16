<?php

namespace App\Admin\Libs;

use Closure;
use Encore\Admin\Admin;
use App\Admin\Libs\Tree\Actions;

use Encore\Admin\Tree as AdminTree;

class Tree extends AdminTree
{
    
    protected $view = [
        'tree'   => 'custom_admin.tree',
        'branch' => 'custom_admin.tree.branch',
    ];

    protected $actionCallbacks = null;

    public function model() {
        return $this->model;
    }
    /**
     * Build tree grid scripts.
     *
     * @return string
     */
    protected function script()
    {
        $trans = [
            'delete_confirm'    => str_replace("'", "\'", trans('admin.delete_confirm')),
            'save_succeeded'    => str_replace("'", "\'", trans('admin.save_succeeded')),
            'refresh_succeeded' => str_replace("'", "\'", trans('admin.refresh_succeeded')),
            'delete_succeeded'  => str_replace("'", "\'", trans('admin.delete_succeeded')),
            'confirm'           => str_replace("'", "\'", trans('admin.confirm')),
            'cancel'            => str_replace("'", "\'", trans('admin.cancel')),
        ];

        $nestableOptions = json_encode($this->nestableOptions);

        $url = url($this->path);

        return <<<SCRIPT

        $('#{$this->elementId}').nestable($nestableOptions);

        $('.tree_branch_delete').click(function() {
            var id = $(this).data('id');
            swal({
                title: "{$trans['delete_confirm']}",
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
                            url: '{$url}/' + id,
                            data: {
                                _method:'delete',
                                _token:LA.token,
                            },
                            success: function (data) {
                                $.pjax.reload('#pjax-container');
                                toastr.success('{$trans['delete_succeeded']}');
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

        $('.{$this->elementId}-save').click(function () {
            var serialize = $('#{$this->elementId}').nestable('serialize');

            $.post('{$url}', {
                _token: LA.token,
                _order: JSON.stringify(serialize)
            },
            function(data){
                $.pjax.reload('#pjax-container');
                toastr.success('{$trans['save_succeeded']}');
            });
        });

        $('.{$this->elementId}-refresh').click(function () {
            $.pjax.reload('#pjax-container');
            toastr.success('{$trans['refresh_succeeded']}');
        });

        $('.{$this->elementId}-tree-tools').on('click', function(e){
            var action = $(this).data('action');
            if (action === 'expand') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse') {
                $('.dd').nestable('collapseAll');
            }
        });


SCRIPT;
    }

    public function resolveActions () {

        return function ($branch) {

            $actionsClass = Actions::class;

            $actions = new $actionsClass($this->path, $this->model->getKeyName(), $branch, $this);

            $this->callActionsCallbacks($actions);

            return $actions->render();
        };
    }

    protected function callActionsCallbacks(Actions $actions)
    {
        foreach ($this->actionCallbacks as $callback) {
            call_user_func($callback, $actions->branch, $actions);
        }
    }

    public function actions($callback)
    {
        if ($callback instanceof \Closure) {
            $this->actionCallbacks[] = $callback;
        }

        return $this;
    }

    /**
     * Render a tree.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function render()
    {
        Admin::script($this->script());

        view()->share([
            'path'           => $this->path,
            'keyName'        => $this->model->getKeyName(),
            'branchView'     => $this->view['branch'],
            'branchCallback' => $this->branchCallback,
            'resolveActions' => $this->resolveActions()
        ]);

        return view($this->view['tree'], $this->variables())->render();
    }
}
