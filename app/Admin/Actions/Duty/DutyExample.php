<?php

namespace App\Admin\Actions\Duty;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class DutyExample extends BatchAction
{
    public $name = "<a href='/statics/excel/duty-example.xlsx' target='_blank' class='salary-export btn btn-sm btn-success'>&nbsp;<i class='fa fa-download'>下载值班模版</i></a>";
    protected $selector = '.duty-example';

    public function handle(Collection $collection)
    {
        // foreach ($collection as $model) {
            
        // }
        
        return $this->response()->success('Success message...');
    }

}