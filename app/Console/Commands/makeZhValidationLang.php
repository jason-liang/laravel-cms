<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class makeZhValidationLang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:lang:zh-CN';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成中文';

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
        $sourcePath = resource_path('lang/zh-CN/php.json');
        $json = File::get($sourcePath);

        $json = json_decode($json, true);
        
        $templatePath = resource_path('lang/zh-CN/validation_template.php');
        $validationArr = require_once($templatePath);

        $target = [];
        foreach ($json as $key => $value) {
            $keys = explode($key, '.');
            
            $value = str_replace(':Attribute', ':attribute', $value);

            if (count($keys) == 2) {
                $key1 = $keys[0];
                $key2 = $keys[1];

                if (isset($validationArr[$key1][$key2])) {
                    $target[$key1][$key2] = $value;
                }

                continue;
             
            } 
              
            if (isset($validationArr[$key])) {
                $target[$key] = $value;
            }
        }

        $targetPath = resource_path('lang/zh-CN/validation.php');

        $target = '<?php ' . PHP_EOL . 'return ' . var_export($target, true) . ';';

        File::replace($targetPath, $target);

        return 0;
    }
}
