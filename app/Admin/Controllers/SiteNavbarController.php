<?php

namespace App\Admin\Controllers;

use App\Models\Navbar;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

use App\Models\SiteNavbar;

class SiteNavbarController extends AdminController
{

    protected $title = '导航设置';

    public function index(Content $content) 
    {
        $tree = new Tree(new SiteNavbar);
        // $tree->disableCreate();
        
        $tree->branch(function ($branch) {
            $html = "{$branch['id']}. <span>{$branch['name']}</span>";

            if (isset($branch['children'])) {
                return $html;
            }
            
            return $html . "&nbsp;<span style='text-decoration: underline;'>{$branch['url']}</span>";
        });

        return $content
                ->header($this->title())
                ->body($tree);
    }

    protected function form()
    {
        $form = new Form(new SiteNavbar());

        $form->display('id', 'ID');
        $form->select('parent_id', '父导航')->options(SiteNavbar::selectOptions());
        $form->text('name', '导航名称')->required()->rules('required');
        $form->text('url', '导航链接');

        return $form;
    }


    public function destroy($id) {
        
        $navbar = SiteNavbar::with('children')->find($id);

        if ($navbar->children()->exists()) {
            return response()->json([
                'status' => false,
                'message' => '该导航下还有子导航，无法删除！'
            ]);
        }

        parent::destroy($id);
    }
}
