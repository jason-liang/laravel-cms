<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware')
], function (Router $router) {

    // 首页
    $router->get('/', 'HomeController@index');
    $router->post('clear-page-cache', 'HomeController@cache');
    // 值班管理
    $router->resource('on-duties', OnDutyController::class); 
    $router->resource('on-duty-imports', OnDutyImportController::class, [ 'only' => ['create', 'store'] ]);
    // 通讯录
    $router->resource('contacts', ContactController::class);
    // 局长信箱
    $router->resource('postboxes', PostboxController::class);
    // 用户管理：重构
    $router->resource('auth/users', AdminUserController::class);
    // 专题管理
    $router->resource('topics', TopicController::class);
    // 栏目管理
    $router->resource('categories', CategoryController::class, ['except' => ['create']]);
    // 文章管理
    $router->resource('articles', ArticleController::class);
    // 文章审核
    $router->group([
        'middleware' => 'admin.permission:allow,administrator',
    ], function ($router) {
    
        // 未审核信息管理
        $router->resource('articles-no-check', ArticleNoCheckController::class)
            ->names('admin.articles.no_check')
            ->except(['create', 'store', 'edit', 'destory']);
    
    });
    // 网站设置
    $router->get('site-settings', 'SiteSettingController@index');
    // 导航
    $router->resource('site-navbars', SiteNavbarController::class);
    // 友情链接
    $router->resource('site-friend-links', SiteFriendLinkController::class);

    /* 
    * 通用控制器
    */
    // 上传
    $router->post('upload', 'UploadController@upload')->name('admin.upload');
});
