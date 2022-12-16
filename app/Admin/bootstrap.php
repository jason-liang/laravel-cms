<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Use Encore\Admin\Admin;
use Encore\Admin\Facades\Admin as AdminFacade;
use App\Admin\Actions\Cache\ClearPageCache;

// 全局修改样式
$style = <<<EOD
.dd-handle:hover {
  color: initial !important;
} 
.dd-item .title-link {
  text-decoration: underline;
}
.table>tbody>tr>td {
  vertical-align: middle;
EOD;
Admin::style($style);

app('view')->prependNamespace('admin', resource_path('views/admin'));

$user = AdminFacade::user();
if ($user && $user->isAdministrator()) {
  AdminFacade::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {

    $navbar->right((new ClearPageCache())->render());

  });
}

Encore\Admin\Form::forget(['map', 'editor']);
Encore\Admin\Form::extend('largefile', \Encore\LargeFileUpload\LargeFileField::class);

