<?php

namespace App\Admin\Controllers;

use App\Models\OnDuty;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\MessageBag;

class OnDutyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '值班管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OnDuty);

        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->tools(function ($tools) {
            $tools->append("<a href='/admin/on-duty-imports/create' class='btn btn-primary'>导入</a>");
        });
        
        $grid->id('ID');
        $grid->model()->orderBy('date', 'desc');
        $grid->date('值班日期')->expand(function ($model) {
            return "<div style='padding: 20px 20px;background: #fff;margin-bottom: 10px;'>$model->content</div>";
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(OnDuty::findOrFail($id));

        $show->id('ID');
        $show->date('日期');
        $show->content('值班详情');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new OnDuty);

        $form->date('date', '值班日期')
            ->default(date('Y-m-d'))
            ->creationRules(['required', 'unique:on_duties,date'])
            ->updateRules(['required', 'unique:on_duties,date,{{id}}']);
        $form->textarea('content', '详情')->rows(10)->required()->rules('required');

        return $form;
    }
}
