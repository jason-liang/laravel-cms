<?php

namespace App\Admin\Controllers;

use Illuminate\Support\Facades\File;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Box;

use App\Admin\Libs\Tree;

use App\Models\Category;
use App\Models\Article;

class CategoryController extends AdminController
{
    
    protected $title = '内容管理';

    public function index(Content $content) {

        $user = Admin::user();
        $isAdmin = $user->isAdministrator();

        $tree = new Tree(new Category);
        $tree->disableCreate();

        if (! $isAdmin) {
            $tree->disableSave();
            $tree->disableRefresh();
        }
        
        $tree->query(function ($model) use ($user) {
            
            if (! $user->isAdministrator()) {
                $cateIds = $user->categories()->get()->pluck('id');
            
                if (! empty($cateIds)) {
                    return $model->whereIn('id', $cateIds)
                                ->orWhereIn('parent_id', $cateIds);
                }
            }

            return $model;
        });
        
        $tree->branch(function ($branch) {

            if (isset($branch['children'])) {
                return "{$branch['id']}. <span>{$branch['name']}</span>";
            }

            $html = '';

            $url = url("/categories/{$branch['id']}/1");
            $html .= "<span style='margin-right: 10px;'>{$branch['id']}. <a href='{$url}' target='_blank' class='dd-nodrag title-link' title='点击查看{$branch['name']}下的所有文章'>{$branch['name']}</a>&nbsp;({$branch['article_amounts']})</span>";
            
            $is_hot = Article::where('category_id', $branch['id'])->where('is_hot', true)->exists();
            $is_sticky = Article::where('category_id', $branch['id'])->where('is_sticky', true)->exists();
            $is_recommend = Article::where('category_id', $branch['id'])->where('is_recommend', true)->exists();
            
            $html .= ($is_hot ? '<span class="label label-danger" style="margin-right: 2px;">热点文章</span>' : '');
            $html .= ($is_sticky ? '<span class="label label-warning" style="margin-right: 2px;">置顶文章</span>' : '');
            $html .= ($is_recommend ? '<span class="label label-success" style="margin-right: 2px;">推荐文章</span>' : '');
            
            return $html;
        });

        $tree->actions(function ($branch, $actions) use ($isAdmin) {
            if (! $isAdmin) {
                $actions->disableAllActions();
            }

            if (! isset($branch['children'])) {
                $url = admin_url("articles?category_id={$branch['id']}");
                $copyLinkBtn = <<<EOD
            <a href="{$url}" class="text-success" style="margin-right: 5px;">
                <i class="fa fa-list fa-lg" style="vertical-align: text-bottom;"></i>
            </a>
EOD;

                $actions->prepend($copyLinkBtn);
            }
        });

        $unchecked = Article::where('status', 0)->count();
        $extra = <<<EOD
        &nbsp;
        <a 
            href="/admin/articles-no-check" 
            style="font-size: 16px;text-decoration: underline;cursor:pointer;"
        >
            待审核({$unchecked})
        </a>
EOD;

        $this->title = $isAdmin ? ($this->title . $extra) : $this->title;

        return $content
                ->header($this->title())
                ->row(function (Row $row) use ($tree, $isAdmin) {

                    $row->column($isAdmin ? 7 : 12, $tree);

                    if ($isAdmin) {
                        $row->column(5, function (Column $column) {
                            $form = new \Encore\Admin\Widgets\Form();
                            $form->action(admin_url('categories'));
                            
                            $form->display('id', __('id'));
                            $form->select('parent_id', '父级栏目')->options(Category::selectOptions());
                            $form->text('name', '名称')->required()->rules('required');
                            $form->image('image', '缩略图')->uniqueName();
                            $form->textarea('desc', '简介');

                            $templates = $this->getTemplates();
                            $form->select('template', '栏目模版')
                                ->options($templates)
                                ->default('category-default.blade.php');
                            $form->radio('banner_type', 'banner类型')->options([
                                1 => '全局banner',
                                2 => '继续父栏目',
                                3 => '自定义',
                            ])->when(3, function (\Encore\Admin\Widgets\Form $form) {
                                $form->image('banner_file', 'banner图片')->uniqueName();
                            })->default(1);
    
                            $form->hidden('_token')->default(csrf_token());
    
                            $column->append((new Box(trans('admin.new'), $form))->style('success'));
                        });
                    }
                    
                });
    }

    protected function form()
    {
        $form = new Form(new Category);   

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
        });

        $form->footer(function ($footer) {
            $footer->disableReset();
        });
        
        $form->display('id', __('id'));
        $form->select('parent_id', '父级栏目')->options(Category::selectOptions());
        $form->text('name', '名称')->required()->rules('required');
        $form->image('image', '缩略图')->uniqueName();
        $form->textarea('desc', '简介');

        // 获取模版名称
        $templates = $this->getTemplates();
        $form->select('template', '栏目模板页')
            ->options($templates)
            ->default('category-default.blade.php');

        $form->radio('banner_type', 'banner类型')->options([
            1 => '全局banner',
            2 => '继续父栏目',
            3 => '自定义',
        ])->when(3, function (Form $form) {
            $form->image('banner_file', 'banner图片(尺寸:询问设计师)')->uniqueName();
        })->default(1);

        return $form;
    }

    public function destroy($id) {
        
        $cate = Category::with('children')->find($id);

        if ($cate->children()->exists()) {
            return response()->json([
                'status' => false,
                'message' => '该栏目下还有子栏目，无法删除！'
            ]);
        }

        if ($cate->articles()->exists()) {
            return response()->json([
                'status' => false,
                'message' => '该栏目下还没有文章，无法删除！'
            ]);
        }

        parent::destroy($id);
    }

    private function getTemplates () {
                
        $templateFiles = File::files(resource_path('views/site/categories'));
        $templates = [];
        
        foreach ($templateFiles as $file) {
            $filename = $file->getRelativePathname();
            $templates[$filename] = $filename;
        }

        return $templates;
    }
}
