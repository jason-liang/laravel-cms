<?php

namespace App\Admin\Actions\Article;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Topic;
use Illuminate\Http\Request;

class BatchRemoveFromTopic extends BatchAction
{
    public $name = '从专题移除文章';

    protected $selector = '.remove-from-topic';

    protected $topicId;

    public function __construct($topicId = null)
    {
        parent::__construct();
        $this->topicId = $topicId;
    }

    public function handle(Collection $collection, Request $request)
    {  
        $topicId = $request->get('topic_id');

        $topic = Topic::findOrFail($topicId);

        $topic->articles()->detach($collection->pluck('id'));

        return $this->response()->success('移除成功')->refresh();
    }

    public function html()
    {
        return "<a class='remove-from-topic btn btn-sm btn-primary'>从专题批移除文章</a>";
    }

    public function parameters()
    {
        
        $parameters = array_merge(parent::parameters(), ['topic_id' => $this->topicId]);

        return $parameters;
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