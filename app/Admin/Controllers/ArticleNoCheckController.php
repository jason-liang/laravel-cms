<?php

namespace App\Admin\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\MessageBag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
// use App\Admin\Libs\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Layout\Content;

use App\Admin\Actions\Article\ChangeStatus;
use App\Admin\Actions\Article\Check;
use App\Admin\Actions\Article\UnCheck;

use App\Models\Article;
use App\Models\Category;

class ArticleNoCheckController extends AdminController
{
    
    protected $title = '待审核文章';

    private $article_types = [];

    public function __construct()
    {
        $this->article_types = config('enums.article_types');
    }

    protected function grid()
    {
        $grid = new Grid(new Article);

        $grid->disableExport();
        $grid->disableFilter();

        $grid->quickSearch('title');
        
        $grid->disableCreateButton();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->add(new ChangeStatus);
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
                $categoryList = Category::get()->pluck('name', 'id');

                $filter->equal('category_id', '栏目')
                    ->select($categoryList);
            });
        });

        $grid->model()
            ->where('status', 0)
            ->orderBy('id', 'desc');

        $grid->id('ID');
        $grid->type('类型')
            ->using($this->article_types);
        $grid->title('标题');
        $grid->category()
            ->name('栏目')
            ->label('default');
        $grid->author()
            ->name('作者');
        $grid->updated_at('更新时间');

        $grid->status('状态')
            ->using([ 
                0 => '待审核'
            ])
            ->label([
                0 => 'default'
            ]);

        return $grid;
    }

    protected function detail($id)
    {
        $user = Admin::user();
        $article = Article::findOrFail($id);
        $show = new Show($article);

        $show->panel()
            ->tools(function ($tools) use ($article, $user) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();

                if ($user->isAdministrator()) {
                    $tools->append(new Check($article));
                    $tools->append(new UnCheck($article));
                }
            });
            
        $show->id('ID');
        $show->title('标题');
        $show->type('类型')->using($this->article_types);
        $show->title('标题');
        $show->thumb('缩略图')->image();
        $show->desc('简介');

        $type = $article->type;
        if ($type === 2) {
            $show->images('图片集合')->carousel();
        } if ($type === 3) {
            $show->video('视频')->as(function ($video) {
                return $video;
            });
        } else if ($type === 4) {
            $show->download('下载')->file();
        } else if ($type === 5) {
            $show->link('外链');
        }

        $show->content('内容')->unescape();

        return $show;
    }

    // 修改status状态是ajax方式，所以不走form，直接放回json
    public function update($id)
    {
        $article = Article::findOrFail($id);
        $status = Request::get('status', 0);

        $article->status = $status;
        $article->save();

        $message = "";
        if ($status == -1) {
            $message = "成功拒绝该文章";
        } else if ($status === 1) {
            $message = "审核通过";
        }

        return response()->json([
            "status" => true,
            "message" => $message
        ]);
    }
}
