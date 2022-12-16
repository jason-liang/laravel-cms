<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use App\Models\SiteBaseSetting as SiteBaseSettingModel;

class SiteBaseSetting extends Form
{

    public $title = '基本设置';

    private const SWITCH_STATES = [
        'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '否', 'color' => 'danger']
    ];

    public function handle(Request $request)
    {
        $data = $request->only('enabled', 'title', 'desc', 'favicon', 'banner', 'theme', 'custom_theme_color');
        
        $data['enabled'] = $data['enabled'] === 'on' ? 1 : 0;
        $data['custom_theme_color'] = $data['theme'] === 'other' ? $data['custom_theme_color'] : null;

        SiteBaseSettingModel::updateOrCreate([
            'id' => 1
        ], $data);

        admin_success('网站基本设置成功');

        return back();
    }

    public function form()
    {
        $this->disableReset();

        $this->switch('enabled', '启用网站')
            ->states(static::SWITCH_STATES);
        $this->text('title', '网站标题')->icon('fa-globe')->rules('required');
        $this->text('desc', '网站描述')->icon('fa-tags');
        $this->image('favicon', '网站图标')->uniqueName();
        $this->image('banner', '网站横幅')->uniqueName();
        $this->select('theme', '网站主题')
            ->options([ 
                'default' => '默认主题', 
                'red' => '节日主题', 
                'grey' => '哀悼日主题',
                'other' => '其他'
            ])->when('other', function (Form $form) {
                $this->color('custom_theme_color', '自定义主题色');
            });
    }

    public function data()
    {
        $defaults = [
            'enabled' => true,
            'title' => 'JCMS内容管理系统',
            'theme' => 'default',
            'custom_theme_color' => '#ffffff'
        ];

        $settings = SiteBaseSettingModel::find(1);

        if ($settings) {
            return $settings ?? $defaults;
        }

        return $defaults;
    }
}
