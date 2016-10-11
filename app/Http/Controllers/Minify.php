<?php
namespace App\Http\Controllers;

class Minify extends Controller
{
    public function run($type)
    {
        $content    = '';
        $lang_files = '';
        $files      = request('files');
        if (!$files) {
            return '';
        }

        switch ($type) {
            case 'css':
                $Min          = new \App\Library\CssMin();
                $content_type = 'text/css;charset=utf-8';
                break;
            case 'js':
                $Min          = new \App\Library\JSMin('');
                $content_type = 'application/x-javascript;charset=utf-8';
                //js类型引入语言包
                $lang_files = request('lang');
                $lang_arr   = [];
                foreach (explode(',', $lang_files) as $lang) {
                    $lang_arr[$lang] = trans($lang);
                }
                if ($lang_arr) {
                    $content .= 'var lang = ' . json_encode($lang_arr) . ';';
                }
                break;
            default:
                return '';
        }

        $cache_time          = config('system.minify_cache_time');
        $resource_cache_name = md5('Minify_' . $type . '|' . $files . '|' . $lang_files);
        $resource_cache      = cache($resource_cache_name);
        $current_time        = \Carbon\Carbon::now()->getTimestamp();
        $last_change_time    = $resource_cache['time'] ? $resource_cache['time'] : $current_time;

        $request          = request();
        $page_was_updated = ($request->hasHeader('If-Modified-Since') and strtotime($request->header('If-Modified-Since')) == $last_change_time);
        $etag_match       = ($request->hasHeader('If-None-Match') and $request->header('If-None-Match') == $resource_cache_name);

        //debug remove test cache
        if (config('app.debug') and $etag_match and !$resource_cache) {
            $page_was_updated = $etag_match = false;
        }
        if (config('app.debug')) {

        }

        if ($page_was_updated or $etag_match) {
            return response('', 304)
                ->header('ETag', $resource_cache_name)
                ->header('Connection', 'close');
        } else {
            if ($resource_cache) {
                $content = $resource_cache['content'];
            } else {

                $filesystem = new \Illuminate\Filesystem\Filesystem();
                foreach (explode(',', $files) as $file) {
                    //防止使用父级目录
                    if (false !== strrpos($file, '..')) {
                        continue;
                    }

                    //压缩文件
                    $file_path = public_path($type . '/' . $file . '.' . $type);
                    if (false === strrpos($file, '.min')) {
                        $cache_name    = md5('Minify_' . $file_path);
                        $cache_content = cache($cache_name);
                        if (!$cache_content) {
                            $file_content  = $filesystem->get($file_path);
                            $cache_content = $Min->minify($file_content);
                            cache([$cache_name => $cache_content], $cache_time / 60);
                        }
                    } else {
                        $cache_content = $filesystem->get($file_path);
                    }
                    $content .= $cache_content;
                }
                cache([$resource_cache_name => ['content' => $content, 'time' => $last_change_time]], $cache_time / 60);

            }
            return response($content, 200)
                ->header('Content-Type', $content_type)
                ->header('Content-Length', strlen($content))
                ->header('ETag', $resource_cache_name)
                ->header('Cache-Control', ' max-age=' . $cache_time)
                ->header('Expires', gmdate("D, d M Y H:i:s", time() + $cache_time) . " GMT")
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_change_time) . ' GMT');
        }
    }
}