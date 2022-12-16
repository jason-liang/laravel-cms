<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;

use App\Admin\Actions\Article\RemoveFromTopic;
use App\Admin\Actions\Article\BatchRemoveFromTopic;
use App\Admin\Actions\Article\ManageArticleInToptic;
use App\Admin\Actions\Article\ArticleEditLink;

use App\Models\Topic;
use App\Models\Article;

class TopicController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '专题管理';


    private const SWITCH_STATES = [
        'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '否', 'color' => 'danger']
    ];

    protected $descriptions = [
        'index' => '专题列表'
    ];

    protected function grid()
    {
        $grid = new Grid(new Topic);

        $grid->disableExport();
        $grid->disableFilter();
        $grid->disableRowSelector();

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->add(new ManageArticleInToptic());
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
        });

        $grid->model()
            ->orderBy('id', 'desc');

        $grid->order('排序')
            ->editable()
            ->sortable();
        $grid->image('缩略图')->image(null, 200, 100);
        $grid->name('标题');
        $grid->created_at('创建时间');
        $grid->article_amounts('专题文章数');
        $grid->enabled('是否上架')
            ->switch(static::SWITCH_STATES);

        return $grid;
    }

    public function show($id, Content $content)
    {
        $topic = Topic::findOrFail($id);

        return $content
            ->title($topic->name)
            ->description('专题文章管理')
            ->body($this->detail($id));
    }

    protected function detail($id) {
        $grid = new Grid(new Article);

        $grid->disableExport();
        $grid->disableFilter();
        $grid->disableCreateButton();

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
            $tools->append("<a class='btn btn-sm btn-success' href='/admin/categories'>添加文章</a>");
        });

        $grid->batchActions(function ($batch) use ($id)  {
            $batch->add(new BatchRemoveFromTopic($id));
        });

        $grid->actions(function ($actions) use ($id) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();

            $actions->add(new ArticleEditLink());
            $actions->add(new RemoveFromTopic($id));
        });

        $grid->model()
            ->whereHas('topics', function ($query) use ($id) {
                $query->where('topics.id', $id);
            });

        $grid->id('ID');
        $grid->title('标题');
        $grid->category()->name('栏目')->label('primary');;
        $grid->author()->name('作者');
        $grid->topics('所属专题')->display(function ($topics) {
            $topics = array_map(function ($t) {
                return "<span class='label label-default'>{$t['name']}</span>";
            }, $topics);

            return join('&nbsp;', $topics);
        });

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Topic);   

        $form->footer(function ($footer) {
            $footer->disableReset();
        });
        
        $form->text('name', '名称')->required()->rules('required');
        $form->image('image', '缩略图')->uniqueName();
        $form->textarea('desc', '简介');
        $form->image('banner_file', '专题横幅');
        $form->switch('enabled', '是否上线')
            ->states(static::SWITCH_STATES)
            ->default(false);

        $form->deleting(function ($id) {
            throw new \Exception('产生错误！！');
            $topic = Topic::findOrFail($id);
            
            if ($topic->articles()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => '该专题下还有文章，无法删除！'
                ]);
            }
        });

        return $form;
    }
}
