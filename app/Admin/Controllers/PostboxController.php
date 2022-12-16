<?php

namespace App\Admin\Controllers;

use App\Models\Postbox;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Request;

class PostboxController extends AdminController
{
    protected $title = '局长信箱';

    private const SWITCH_STATES = [
        'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '否', 'color' => 'danger']
    ];

    protected function grid()
    {
        $grid = new Grid(new Postbox());

        $grid->disableExport();
        $grid->disableRowSelector();
        
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->id('ID');
        $grid->title('信件标题');
        $grid->fullname('发件人');
        $grid->created_at('发件时间');
        $grid->is_reply('是否回复')
            ->using([
                0 => '未回复',
                1 => '已回复'
            ])->label([
                0 => 'default',
                1 => 'success'
            ]);
        $grid->reply_department('回复部门')->default('-');
        $grid->reply_time('回复时间')->default('-');
        $grid->status('是否对外公开显示')
            ->using([
                0 => '不显示',
                1 => '显示'
            ])->label([
                0 => 'danger',
                1 => 'success'
            ])->filter([
                0 => '不显示',
                1 => '显示'
            ]);

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Postbox::findOrFail($id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });
            
        $show->id('ID');
        $show->title('信件标题');
        $show->fullname('姓名');
        $show->content('内容');
        $show->created_at('发件时间');
        $show->divider();
        $show->is_reply('是否已回复')
            ->using([
                0 => '未回复',
                1 => '已回复'
            ])->as(function ($is_reply) {
                if ($is_reply) {
                    return "<span class='label label-success'>已回复</span>";
                }

                return "<span class='label label-danger'>未回复</span>";
            })->unescape();
        $show->reply_department('回复部门');
        $show->reply_content('回复内容');
        $show->reply_time('回复时间');
        $show->updated_at('更新时间');

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Postbox());
        
        $form->tools(function ($tools) {
            $tools->disableDelete();
        });
        
        // $form->text('title', '信件标题')->icon('fa-file-text')->readonly();
        // $form->text('fullname', '发件人')->icon('fa-user')->readonly();
        // $form->textarea('content', '信件内容')->readonly();
        $form->text('title', '信件标题')->icon('fa-file-text')->required();
        $form->text('fullname', '发件人')->icon('fa-user')->required();
        $form->textarea('content', '信件内容')->required();
        $form->radio('is_reply', '是否回复')
            ->options([
                0 => '否',
                1 => '是'
            ])
            ->when(0, function (Form $form) {
                $form->hidden('reply_department');
                $form->hidden('reply_content');
                $form->hidden('status')->default(false);
            })
            ->when(1, function (Form $form) {
                $form->text('reply_department', '回复部门');
                $form->textarea('reply_content', '回复内容');
                $form->switch('status', '是否对外公开显示')
                    ->states(static::SWITCH_STATES)
                    ->default(true);
        });

        $form->hidden('reply_time');

        $form->saving(function (Form $form) {
            // 如果是行内编辑不走下面的校验
            if (Request::ajax() && !Request::pjax()) {
                return true;
            }

            if ($form->is_reply) {
                $form->reply_time = date('Y-m-d H:i:s');

                if (empty($form->reply_department)) {
                    $error = new MessageBag([
                        'title'   => '错误',
                        'message' => '回复部门不能为空',
                    ]);
    
                    return back()->withInput()->with(compact('error'));
                }

                if (empty($form->reply_content)) {
                    $error = new MessageBag([
                        'title'   => '错误',
                        'message' => '回复内容不能为空',
                    ]);
    
                    return back()->withInput()->with(compact('error'));
                }

            } else {
                $form->status = false;
                $form->reply_department = null;
                $form->reply_content = null;
            }
        });

        return $form;
    }
}
