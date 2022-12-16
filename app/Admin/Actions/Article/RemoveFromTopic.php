<?php

namespace App\Admin\Actions\Article;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Topic;

class RemoveFromTopic extends RowAction
{
    public $name = '从专题中移出';

    protected $topicId;

    public function __construct($topicId = null)
    {
        parent::__construct();
        $this->topicId = $topicId;
    }

    public function handle(Model $model, Request $request)
    {
        $topicId = $request->get('topic_id');
        $topic = Topic::findOrFail($topicId);
        
        $topic->articles()->detach($topicId);

        return $this->response()->success('移出成功')->refresh();
    }

    public function dialog() {
        $this->confirm('确定将文章移出专题吗？');
    }

    public function parameters()
    {
        $parameters = array_merge(parent::parameters(), ['topic_id' => $this->topicId]);

        return $parameters;
    }

}