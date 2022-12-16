<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OnDuty;
use App\Models\OnDutyImport as OnDutyImportModel;
use App\Admin\Imports\OnDutyImport;
use App\Admin\Actions\Duty\DutyExample;
use Illuminate\Support\MessageBag;
use DB;
use Log;

class OnDutyImportController extends AdminController
{
    protected $title = '值班表导入';

    public function __construct()
    {
        // $this->description['create'] = '1';
    }

    protected function form()
    {

        $form = new Form(new OnDutyImportModel);

        $form->largefile('file', '值班表Excel文件')->required()->group('file');

        $form->saving(function (Form $form) {
            if(!$form->file) {
                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => '请上传值班表',
                ]);

                return back()->withInput()->with(compact('error'));
            }

            DB::transaction(function () use ($form){

                try {
                    Excel::import(new OnDutyImport, 'storage/uploads/aetherupload/' . str_replace('_', '/', $form->file));
                } catch (\Exception $e) {

                    $error = new MessageBag([
                        'title'   => 'Excel导入错误',
                        'message' => $e->getMessage(),
                    ]);

                    return back()->with(compact('error'));
                }
            });

            admin_toastr('上传成功', 'success');
       
            return redirect(admin_url('on-duties'));
        });

        return $form;
    }
}
