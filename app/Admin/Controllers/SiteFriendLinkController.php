<?php

namespace App\Admin\Controllers;

use App\Models\FriendLink;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

use App\Models\SiteFriendLink;

class SiteFriendLinkController extends AdminController
{

    protected $title = '友情链接';

    public function index(Content $content) 
    {
        $tree = new Tree(new SiteFriendLink);
        
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
        $form = new Form(new SiteFriendLink());

        $form->display('id', 'ID');
        $form->select('parent_id', '父节点')->options(SiteFriendLink::selectOptions());
        $form->text('name', '名称')->required()->rules('required');
        $form->text('url', '链接');

        return $form;
    }


    public function destroy($id) {
        
        $navbar = SiteFriendLink::with('children')->find($id);

        if ($navbar->children()->exists()) {
            return response()->json([
                'status' => false,
                'message' => '该节点下还有子节点，无法删除！'
            ]);
        }

        parent::destroy($id);
    }
}
