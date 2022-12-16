<?php

namespace App\Admin\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\MessageBag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Libs\Grid as MyGrid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Admin\Actions\Article\BatchAddToTopic;
use App\Models\Article;
use App\Models\Category;

class ArticleController extends AdminController
{

    protected $title = '文章管理';

    private const CHECK_STATUS = [ 
        -1 => '未通过',
        0 => '待审核', 
        1 => '审核通过'
    ];

    private const SWITCH_STATES = [
        'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '否', 'color' => 'danger']
    ];

    private $article_types = [];

    public function __construct()
    {
        $this->article_types = config('enums.article_types');
    }

    /* 
    * 覆写列表页，主要为了动态改变title
    */
    public function index(Content $content)
    {
        $categoryId = (int)Request::get('category_id', 0);

        $r = $this->checkUserCategoryPermission($categoryId);
        if (! $r['status']) {
            return $content->body($r['content']);
        }

        // 获取category
        $title = $r['content']->name;

        return $content
            ->title($title)
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid($categoryId));
    }

    protected function grid($categoryId)
    {
        $user = Admin::user();
        $isAdmin = $user->isAdministrator();

        $grid = new MyGrid(new Article);

        $grid->disableExport();
        $grid->disableFilter();

        $grid->quickSearch('title');
        $grid->setCreateBtnQueryString('category_id=' . $categoryId);

        $grid->batchActions(function ($batch) {
            // $batch->add(new BatchAddToTopic());
        });

        $grid->tools(function (Grid\Tools $tools) use ($isAdmin) {
            if ($isAdmin) {
                $tools->append(new BatchAddToTopic());
            }
        });

        $grid->actions(function ($actions) use ($user) {
            $actions->disableView();

            // if ($actions->row->admin_user_id !== $user->id) {
            //     $actions->disableEdit();
            //     $actions->disableDelete();
            // }
        });

        $model = $grid->model()
                    ->where('category_id', $categoryId);

        if ($isAdmin) {
            $model->where('status', 1);
        } else {
            $model->orderBy('status', 'asc');
        }

        $model->orderBy('enabled', 'asc')
            ->orderBy('is_sticky', 'desc')
            ->orderBy('is_hot', 'desc')
            ->orderBy('is_recommend', 'desc')
            ->orderBy('order', 'desc')
            ->orderBy('id', 'desc');
        
        $grid->order('排序')
            ->editable()
            ->sortable();
        $grid->id('ID');
        $grid->type('类型')
            ->using($this->article_types);
        $grid->title('标题')->link(function ($row) {
            return "/articles/{$row->id}";
        });
        $grid->category()
            ->name('栏目')
            ->label('primary');
        $grid->author()
            ->name('作者');
        $grid->updated_at('更新时间');
        $grid->status('状态')
            ->using(static::CHECK_STATUS)
            ->label([
                0 => 'default',
                -1 => 'danger',
                1 => 'success'
            ]);

        if ($isAdmin) {
                 
            $grid->sticky_hot_recommend('编辑推荐')
                ->switchGroup([
                    'is_sticky'    => '置顶',
                    'is_hot'       => '热点',
                    'is_recommend' => '推荐'
                ], static::SWITCH_STATES);
            $grid->enabled('是否上架')
                ->switch(static::SWITCH_STATES);
        } else {

            $grid->sticky_hot_recommend('编辑推荐')->display(function () {
                $style = "display:inline-block;margin-bottom: 1px;";
                $sticky_label = "<span class='label label-warning' style='{$style}'>置顶</span><br>";
                $hot_label = "<span class='label label-danger' style='{$style}'>热门</span><br>";
                $recommend_label = "<span class='label label-primary' style='{$style}'>推荐</span><br>";

                $labels = "";
                if ($this->is_sticky)
                    $labels .= $sticky_label;
                
                if ($this->is_hot)
                    $labels .= $hot_label;

                if ($this->is_recommend)
                    $labels .= $recommend_label;

                return strlen($labels) <= 0 ? "-" : $labels;
            });

            $grid->enabled('是否上架')
                ->using([
                    0 => '下架',
                    1 => '上架'
                ])->label([
                    0 => 'danger',
                    1 => 'success'
                ])->filter([
                    0 => '下架',
                    1 => '上架'
                ]);
        }

        return $grid;
    }

    protected function detail($id)
    {
        $article = Article::findOrFail($id);
        $show = new Show($article);

        $show->panel()
            ->tools(function ($tools) {
                // $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
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

    public function create(Content $content)
    {       
        $categoryId = (int)Request::get('category_id', 0);

        $r = $this->checkUserCategoryPermission($categoryId);
        if (! $r['status']) {
            return $content->body($r['content']);
        }
        
        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($this->form($categoryId));
    }

    public function edit($id, Content $content)
    {           
        $article = Article::with('category')->findOrFail($id);
        $categoryId = $article->category->id;

        $r = $this->checkUserCategoryPermission($categoryId);
        if (! $r['status']) {
            return $content->body($r['content']);
        }

        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($this->form($categoryId)->edit($id));
    }

    protected function form($categoryId = null)
    {
        $user = Admin::user();
        $isAdmin = $user->isAdministrator();

        $form = new Form(new Article);

        $form->tools(function ($tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });
        
        $form->text('title', '标题')->rules('required');
        $form->image('thumb', '缩略图')->uniqueName();
        $form->textarea('desc', '简介')->rules('max:500');
        $form->radioButton('type', '文章类型')
            ->options($this->article_types)
            ->when(1, function (Form $form) {})
            ->when(2, function (Form $form) {
                $form->multipleImage('images', '图片集合(多选)')->uniqueName()->removable();
            })
            ->when(3, function (Form $form) {
                $form->radioButton('is_foreign_link', '是否是本地文件')
                    ->options([
                        0 => '是',
                        1 => '否'
                    ])
                    ->when(0, function (Form $form) {
                        $form->largefile('video', '视频')->group('video');
                    })
                    ->when(1, function (Form $form) {
                        $form->text('video', '视频')->placeholder('请输入视频地址');
                    });
            })
            ->when(4, function (Form $form) {
                $form->radioButton('is_foreign_link', '是否是本地文件')
                    ->options([
                        0 => '是',
                        1 => '否'
                    ])
                    ->default(0)
                    ->when(0, function (Form $form) {
                        $form->largefile('download', '下载地址')->group('download');
                    })
                    ->when(1, function (Form $form) {
                        $form->text('download', '下载地址')->placeholder('请输入下载地址');
                    });
            })
            ->when(5, function (Form $form) {
                $form->text('link', '外部链接');
            })
            ->default(1);

        $form->UEditor('content', '文章详情');

        if ($isAdmin) {
            $form->switch('is_sticky', '置顶')
                ->states(static::SWITCH_STATES)
                ->default(false);
            $form->switch('is_hot', '热点')
                ->states(static::SWITCH_STATES)
                ->default(false);
            $form->switch('is_recommend', '推荐')
                ->states(static::SWITCH_STATES)
                ->default(false);
        } else {
            $form->hidden('is_sticky')->default(false);
            $form->hidden('is_hot')->default(false);
            $form->hidden('is_recommend')->default(false);
        }

        // 不想显示的字段使用hidden，不然saving回调里通过$form->fieldname修改将无效
        $form->hidden('status', '审核状态')->default(0);
        $form->hidden('order', '顺序')->default(0);
        $form->hidden('category_id', '栏目')->default($categoryId ?? null);
        $form->hidden('enabled', '是否上架')->default(true);

        $form->saving(function (Form $form) use ($isAdmin) {
  
            // 如果是行内编辑不走下面的校验
            if (Request::ajax() && !Request::pjax()) {
                return true;
            }

            if ($isAdmin) {
                // 管理员审核状态为通过
                $form->status = 1;
            } else {
                if ($form->isCreating()) {
                    $form->is_sticky = false;
                    $form->is_hot = false;
                    $form->is_recommend = false;
                }
                
                // 非管理员重置审核状态
                $form->status = 0;
            }
  
            $msg = '';
            if ($form->type == 1) {
                // 图文
                if (!strlen($form->content)) {
                    $msg = '图文类型的文章的详情不能为空！';
                }

                $form->images = [];
                $form->video = null;
                $form->download = null;
                $form->link = null;
            } else if ($form->type == 2) {
                // 图片
                if (((! $form->images) || count($form->images) <= 0) && count($form->model()->images) <= 0) {
                    $msg = '图片新闻至少需要上传一张图片';
                }

                $form->video = null;
                $form->download = null;
                $form->link = null;
            } else if ($form->type == 3) {
                // 视频
                if (!strlen($form->video)) {
                    $msg = '视频类型的文章必须上传视频！';
                }

                $form->images = [];
                $form->download = null;
                $form->link = null;
            } else if ($form->type == 4) {
                // 下载
                if ((! $form->download) || strlen($form->download) <= 0) {
                    $msg = '下载类型的文章没有下载地址！';
                }

                $form->images = [];
                $form->video = null;
                $form->link = null;
            } else if ($form->type == 5) {
                // 外链
                if (! strlen($form->link)) {
                    $msg = '外链地址不能为空';
                }

                $form->images = [];
                $form->video = null;
                $form->download = null;
            }

            if (!empty($msg)) {
            
                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => $msg,
                ]);
                return back()->withInput()->with(compact('error'));
            }

            if (strlen($form->content) > 320000) {

                $error = new MessageBag([
                    'title'   => '错误',
                    'message' => '内容超出了最大值！',
                ]);

                return back()->withInput()->with(compact('error'));
            }
        });

        $form->saved(function (Form $form) {
            return redirect('/admin/articles?category_id=' . $form->category_id);
        });

        return $form;
    }

    protected function checkUserCategoryPermission($categoryId) {
        $user = Admin::user();
        $isAdmin = $user->isAdministrator();

        $category = Category::find($categoryId);

        if (! $category) {
            
            return [
                'status' => false,
                'content' => alert_error_render('未找到栏目！')
            ];
        }

        if (! $isAdmin) {
            $allowCategoryList = $user->categories()->get()->pluck('id')->toArray();

            if (! in_array($categoryId, $allowCategoryList)) {
                return [
                    'status' => false,
                    'content' => alert_error_render('你没有查看该栏目文章的权限，如有疑问请联系管理员')
                ];
            }
        }

        return [
            'status' => true,
            'content' => $category
        ];
    }
}
