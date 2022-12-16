<?php

namespace App\Admin\Controllers;

use App\Models\Contact;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ContactController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '通讯录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Contact());

        $grid->disableFilter();
        $grid->disableRowSelector();
        
        $grid->quickSearch('fullname');

        $grid->id('ID');
        $grid->fullname('姓名');
        $grid->department('部门');
        $grid->telephone('座机');
        $grid->personal_phone('个人电话');
        $grid->other_phone('其他电话');

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
        $show = new Show(Contact::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('fullname', '姓名');
        $show->field('department', '部门');
        $show->field('telephone', '座机');
        $show->field('personal_phone', '个人电话');
        $show->field('other_phone', '其他电话');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Contact());

        $form->text('fullname', '姓名');
        $form->text('department', '部门');
        $form->mobile('telephone', '座机');
        $form->mobile('inner_phone', '内部电话');
        $form->mobile('personal_phone', '个人电话');
        $form->mobile('other_phone', '其他电话');

        return $form;
    }
}
