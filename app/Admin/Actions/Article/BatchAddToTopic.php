<?php

namespace App\Admin\Actions\Article;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Models\Topic;
use App\Models\TopicArticle;
use DB;
use Log;

class BatchAddToTopic extends BatchAction
{
    public $name = '添加文章到专题';

    protected $selector = '.add-to-topic';
    
    public function handle(Collection $collection, Request $request)
    {
        $topicId = $request->get('topic_id');
        
        $topic = Topic::findOrFail($topicId);
        
        $topic->articles()->syncWithoutDetaching($collection->pluck('id'));

        return $this->response()->success('添加成功')->refresh();
    }

    public function form()
    {
        $topics = Topic::all()->pluck('name', 'id');

        $this->select('topic_id', '选择专题')->options($topics)->required()->rules('required');
    }

    public function html()
    {
        return "<a class='add-to-topic btn btn-sm btn-primary' style='padding-right: 10px;'>批量添加到专题</a>";
    }

    public function actionScript()
    {
        $warning = '没有选择文章';

        return <<<SCRIPT
        var key = $.admin.grid.selected();

        if (key.length === 0) {
            $.admin.toastr.warning('{$warning}', '', {positionClass: 'toast-top-center'});
            return ;
        }

        Object.assign(data, {_key:key});
SCRIPT;
    }

}