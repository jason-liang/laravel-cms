<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;

use App\Admin\Forms\SiteBaseSetting;
use App\Admin\Forms\FloatingWindow;
use App\Admin\Forms\PopupWindow;

class SiteSettingController extends AdminController
{
    protected $title = '网站设置';

    public function index(Content $content) 
    {
        $tabs = Tab::forms([
            'base' => SiteBaseSetting::class,
            'floating_window' => FloatingWindow::class,
            'popup_window' => PopupWindow::class
        ]);

        return $content
            ->title($this->title())
            ->body($tabs);
    }

    protected function detail($id) {}

    protected function form() {}

    protected function grid() {}
}
