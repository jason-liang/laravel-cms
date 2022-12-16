<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateJcmsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 20);
            $table->string('image', 200)->nullable();
            $table->string('desc', 200)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('template')->nullable()->comment('栏目模版文件名');
            $table->unsignedTinyInteger('banner_type')->default(1)->comment('banner类型： 1:全局banner、2:继承父节点、3:自定义');
            $table->string('banner_file')->nullable();
            $table->unsignedInteger('article_amounts')->default(0)->comment('文章数量统计');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('parent_id')->default(0);

            $table->index(['id', 'parent_id']);
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->default(0);
            $table->string('title', 200);
            $table->string('thumb', 200)->nullable();
            $table->string('desc', 500)->nullable();
            $table->longText('content');

            $table->unsignedTinyInteger('type')->default(1)->comment('文章类型');
            $table->unsignedTinyInteger('is_foreign_link')->default(false)->comment('是否是外链');
            $table->json('images')->nullable()->comment('图片新闻资源');
            $table->string('video', 200)->nullable()->comment('video资源地址');
            $table->string('download', 200)->nullable()->comment('download资源地址');
            $table->string('link', 200)->nullable()->comment('link资源地址');

            $table->tinyInteger('status')->default(0)->comment('文章状态：-1: 拒绝，0: 待审核，1: 审核通过');
            $table->boolean('is_hot')->default(false)->comment('是否热点');
            $table->boolean('is_sticky')->default(false)->comment('是否置顶');
            $table->boolean('is_recommend')->default(false)->comment('是否推荐');
            $table->boolean('enabled')->default(true)->comment('是否上架');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('admin_user_id');
            $table->unsignedBigInteger('category_id');

            $table->index([ 'category_id', 'admin_user_id', 'status' ]);
            $table->index('title');
            $table->index([ 'category_id', 'is_hot' ]);
            $table->index([ 'category_id', 'is_sticky' ]);
            $table->index([ 'category_id', 'is_recommend' ]);
        });

        Schema::create('admin_user_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_user_id');
            $table->unsignedBigInteger('category_id');

            $table->index(['admin_user_id', 'category_id']);
        });

        // 专题
        Schema::create('topics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 20);
            $table->string('image', 200)->nullable()->comment('专题缩略图');
            $table->string('desc', 200)->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('banner_file', 200)->nullable()->comment('专题横幅图片');
            $table->unsignedInteger('article_amounts')->default(0)->comment('文章数量统计');
            $table->boolean('enabled')->default(false)->comment('是否上线');

            $table->timestamps();
            $table->softDeletes();
        });

        // 专题文章中间表
        Schema::create('topic_articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('topic_id');
            $table->unsignedBigInteger('article_id');
            $table->unsignedInteger('order')->default(0);

            $table->index(['topic_id', 'article_id']);
        });

        // 通讯录
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->default(0);
            $table->string('fullname')->comment('姓名');
            $table->string('department')->comment('部门');
            $table->string('telephone')->nullable()->comment('座机');
            $table->string('personal_phone')->nullable()->comment('个人电话');
            $table->string('other_phone')->nullable()->comment('其他电话');
            $table->timestamps();
        });

        // 局长信箱
        Schema::create('postboxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->comment('标题');
            $table->string('fullname')->comment('来信人');
            $table->text('content')->comment('内容');
            $table->boolean('is_reply')->comment('是否已回复');
            $table->string('reply_department')->nullable()->comment('回复部门');
            $table->text('reply_content')->nullable()->comment('回复内容');
            $table->datetime('reply_time')->nullable()->comment('回复时间');
            $table->boolean('status')->default(false)->commnet('是否显示');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('on_duties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->unique();
            $table->text('content');
            $table->timestamps();

            $table->index('date');
        });

        Schema::create('on_duty_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file');
            $table->timestamps();
        });

        // 网站设置
        Schema::create('site_base_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('enabled')->default(true)->comment('网站是否启用');
            $table->string('title')->nullable()->comment('网站标题');
            $table->string('desc')->nullable()->nullable()->comment('网站描述');
            $table->string('favicon')->nullable()->comment('网站图标');
            $table->string('theme')->default('default')->comment('网站主题: default, red, grey, other');
            $table->string('custom_theme_color')->nullable()->comment('自定义主题颜色');
            $table->string('banner')->nullable()->comment('网站横幅设置');
            $table->json('floating_window')->nullable()->comment('飘窗广告设置');
            $table->json('popup_window')->nullable()->comment('弹窗广告设置');

            $table->timestamps();
        });

        // 网站导航
        Schema::create('site_navbars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->default(0);
            $table->unsignedTinyInteger('type')->default(1)->comment('type: 1.顶部导航 2.底部导航');
            $table->string('name')->comment('名称');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);

            $table->timestamps();

            $table->index([ 'parent_id', 'type' ]);
        });

        // 友情链接
        Schema::create('site_friend_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order')->default(0);
            $table->string('name')->comment('名称');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);

            $table->timestamps();

            $table->index([ 'parent_id' ]);
        });

        Schema::create('day_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->date('day');
            $table->unsignedInteger('count')->default(0);

            $table->unique('day');
            $table->index(['day', 'type']);
        });

        Schema::create('month_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->date('year_month');
            $table->unsignedInteger('count')->default(0);

            $table->unique('year_month');
            $table->index(['year_month', 'type']);
        });

        Schema::create('year_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->year('year');
            $table->unsignedInteger('count')->default(0);

            $table->unique('year');
            $table->index(['year', 'type']);
        });

        /* 
            生成默认数据
        */
        Artisan::call('db:seed', [
            '--class' => 'SiteMenuSeeder',
            '--force' => true
        ]);
        Artisan::call('db:seed', [
            '--class' => 'SiteSettingSeeder',
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('admin_user_categories');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('topic_articles');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('postboxes');
        Schema::dropIfExists('on_duties');
        Schema::dropIfExists('on_duty_imports');
        Schema::dropIfExists('site_base_settings');
        Schema::dropIfExists('site_navbars');
        Schema::dropIfExists('site_friend_links');
        Schema::dropIfExists('day_statistics');
        Schema::dropIfExists('month_statistics');
        Schema::dropIfExists('year_statistics');
 
    }
}
