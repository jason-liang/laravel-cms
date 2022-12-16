<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Models\Category;
use App\Models\Article;

class PageController extends Controller
{
    protected $excludeDirs = [ 'statics', 'storage', 'vendor' ];

    // get method
    public function get(Request $request) {

        $path = $request->decodedPath();
        
        // 首页
        if ($path === '/') {
            return $this->homepage();
        }

        $params = explode('/', $path);

        $controller = $params[0];
        if ($controller === 'get' || (! method_exists($this, $controller))) {
            return $this->error('404');
        }

        $id = (int)($params[1] ?? 0);
        if (! $id) {
            return $this->error('404');
        }

        $page = (int)($params[2] ?? 1);

        return $this->$controller($id, $page);
    }

    protected function homepage() 
    {
        return $this->generatePage('site.home', []);
    }

    protected function categories($id, $page = 1) 
    {
        $category = Category::with('articles')->findOrFail($id);

        $template = $category->template ?? 'category-default.blade.php';

        if (! File::exists(resource_path('views/site/categories/' . $template))) {
            throw new \Exception('页面模版文件' .$template . '不存在' );
        }

        $filename = explode('.', $template)[0];

        return $this->generatePage('site.categories.' . $filename, compact('category'), "categories/{$id}/{$page}");
    }

    protected function articles($id) 
    {

        $article = Article::with('category', 'author')->where('id', $id)->where('status', 1)->first();

        if (! $article) {
            return $this->error('404');
        }

        return $this->generatePage('site.articles.article', compact('article'), "articles/{$id}");
    }

    protected function topics($id) 
    {
        return 'topics';
    }

    protected function contacts() 
    {
        return 'contacts';
    }

    protected function postboxes($id) 
    {
        return 'postboxes';
    }

    protected function error($error = '404') 
    {
        return view('site.errors.' . $error);
    }

    protected function generatePage($template, $data = [], $relativePath = '') 
    {
        // Log::info('php is running ' . Carbon::now()->format('Y-m-d H:i:s'));
        
        if ($this->isExcludeController($relativePath)) {
            return $this->error('404');
        }

        $view = view($template, $data);
        
        $staticEnabled = env('SITE_STATIC', false);
        $staticExpired = env('SITE_STATIC_EXPIRED', 3600);

        if (! $staticEnabled) {
            return $view;
        }

        $dirpath = storage_path('app/public/html/' . $relativePath);
        $filepath = "{$dirpath}/index.html";

        if (File::exists($filepath)) {
            $filetime = File::lastModified($filepath);
            $nowtime = time();
            
            if ($nowtime - $filetime <= $staticExpired) {
                return File::get($filepath);
            }
        }

        if (! File::isDirectory($dirpath)) {
            File::makeDirectory($dirpath, 0755, true, true);
        }

        File::replace($filepath, $view);

        return $view;
    }

    protected function isExcludeController($relativePath) 
    {
        foreach ($this->excludeDirs as $dir) {
            if (Str::startsWith($relativePath, $dir)) {
                return true;
            }
        }

        return false;
    }
}
