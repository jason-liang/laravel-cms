<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use App\Models\SiteBaseSetting as SiteBaseSettingModel;

class PopupWindow extends Form
{

    public $title = '弹窗设置';

    private const SWITCH_STATES = [
        'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '否', 'color' => 'danger']
    ];

    public function handle(Request $request)
    {
        $data = $request->only('enabled', 'url', 'image', 'is_global');

        $data['enabled'] = $data['enabled'] === 'on' ? 1 : 0;
        $data['is_global'] = (int)$data['is_global'];

        SiteBaseSettingModel::updateOrCreate([
            'id' => 1
        ], [
            'popup_window' => $data
        ]);

        admin_success('弹窗设置成功');

        return back();
    }

    public function form()
    {
        $this->disableReset();
        
        $this->switch('enabled', '启用弹窗')->states(static::SWITCH_STATES);
        $this->image('image', '图片')->uniqueName();
        $this->url('url', '链接地址')->placeholder('http://');
        $this->radio('is_global', '显示模式')->options([
            0 => '仅首页显示',
            1 => '所有页面均显示'
        ]);
    }

    public function data()
    { 
        $defaults = [
            'enabled' => false,
            'image' => null,
            'url' => null,
            'is_global' => 0
        ];

        $settings = SiteBaseSettingModel::find(1);

        if ($settings) {
            return $settings['popup_window'] ?? $defaults;
        }

        return $defaults;
    }
}
