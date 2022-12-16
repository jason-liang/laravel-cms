<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Facades\Admin;

use App\Models\Article;
use App\Models\DayStatistic;
use App\Models\MonthStatistic;
use App\Models\YearStatistic;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('控制面板')
            ->description('&nbsp;')
            ->body(function (Row $row) {
                
                $user = Admin::user();
                if ($user->isAdministrator()) {
                    $row->column(3, function (Column $column) {

                        $count = Article::where('status', 0)
                                        ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                                        ->count();
                        
                        $infoBox = new InfoBox('待审核', 'calendar-check-o', 'blue', admin_url('articles-no-check'), $count);
                        $column->append($infoBox->render());
                    });

                    $row->column(3, function (Column $column) {

                        $day = DayStatistic::whereDate('day', Carbon::now()->format('Y-m-d'))
                                            ->where('type', 'publish_articles')
                                            ->first();

                        $infoBox = new InfoBox('日发布', 'calendar', 'yellow', null, $day ? $day->count : 0);
                        $column->append($infoBox->render());
                    });

                    $row->column(3, function (Column $column) {

                        $month = MonthStatistic::whereYear('year_month', Carbon::now()->format('Y'))
                                                ->whereMonth('year_month', Carbon::now()->format('m'))
                                                ->where('type', 'publish_articles')
                                                ->first();

                        $infoBox = new InfoBox('月发布', 'calendar-minus-o', 'green', null, $month ? $month->count : 0);
                        $column->append($infoBox->render());
                    });

                    $row->column(3, function (Column $column) {

                        $year = YearStatistic::where('year', Carbon::now()->format('Y'))
                                                ->where('type', 'publish_articles')
                                                ->first();

                        $infoBox = new InfoBox('年发布', 'calendar-o', 'purple', null, $year ? $year->count : 0);
                        $column->append($infoBox->render());
                    });
                }
         
                $row->column(4, function (Column $column) {

                    $headers = ['模块', '链接规则'];
                    $rows = [
                        '栏目链接'   => '/categories/{id}/{page}',
                        '文章链接'   => '/articles/{id}/{page}',
                        '专题链接' => '/topics/{id}/{page}',
                        '值班表链接'  => '/on-duties',
                        '通讯录链接'  => '/contacts',
                        '值班表链接'  => '/on-duties',
                        '局长信箱列表链接'  => '/poxboxes',
                        '局长信箱详情链接'  => '/poxboxes/{id}',
                        '局长信箱提交链接'  => '/poxboxes/{id}/create',
                        
                    ];
                    $style = [ 'table-striped' ];
                    
                    $table = new Table($headers, $rows, $style);

                    $box = new Box('页面链接表', $table->render());

                    $box->collapsable();

                    $box->removable();

                    $column->append($box);
                });
                $row->column(8, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                // $row->column(3, function (Column $column) {
                //     $box = new Box(
                //         '小工具', 
                //         '<div style="text-align: center;"><a class="btn btn-success" href="/admin/update-home-cache">清除页面缓存</a></div>'
                //     );
                //     $column->append($box);
                // });

                // $row->column(3, function (Column $column) {
                //     $column->append(Dashboard::dependencies());
                // });

                // $row->column(3, function (Column $column) {
                //     $column->append(Dashboard::extensions());
                // });
            });
    }

    public function cache(Content $content) {

        clear_page_cache();

        return response()->json([
            'status' => true,
            'message' => '首页全站成功！'
        ]);
    }
}
