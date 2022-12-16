<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteBaseSetting as SiteBaseSettingModel;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteBaseSettingModel::truncate();
        SiteBaseSettingModel::create([
            'id' => 1,
            'enabled' => true,
            'title' => 'JCMS内容管理系统',
            'theme' => 'default',
            'custom_theme_color' => '#ffffff',
            'floating_window' => [
                'enabled' => false,
                'image' => null,
                'url' => null,
                'is_global' => 0
            ],
            'popup_window' => [
                'enabled' => false,
                'image' => null,
                'url' => null,
                'is_global' => 0
            ]
        ]);
    }
}
