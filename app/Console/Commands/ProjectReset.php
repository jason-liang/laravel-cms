<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProjectReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isProd = app()->environment('production');

        if ($isProd) {
            $this->error('生产环境不能重制项目');
            return 0;
        }

        if ($this->confirm('你确定要重置项目数据吗?')) {
            
            $this->info('migrate fresh start');
            Artisan::call('migrate:fresh', [
                '--force' => true
            ]);

            $this->info('seed AdminTablesSeeder');
            Artisan::call('db:seed', [
                '--class' => 'AdminTablesSeeder',
                '--force' => true
            ]);

            $this->info('seed SiteMenuSeeder');
            Artisan::call('db:seed', [
                '--class' => 'SiteMenuSeeder',
                '--force' => true
            ]);

            $this->info('admin create-user');
            Artisan::call('admin:create-user');
           
            return 0;
        }

        return 0;
    }
}
