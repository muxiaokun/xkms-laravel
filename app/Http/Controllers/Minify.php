<?php
namespace App\Http\Controllers;

class Minify extends Controller
{
    public function run($type)
    {
        switch ($type) {
            case 'css':
                $Min          = new \App\Library\CssMin();
                $content_type = 'text/css;charset=utf-8';
                break;
            case 'js':
                $Min          = new \App\Library\JSMin('');
                $content_type = 'application/x-javascript;charset=utf-8';
                break;
            default:
                return '';
        }

        $files = request('files');
        if (!$files) {
            return '';
        }

        //TODO debug status
        //TODO include lang

        $cache_time          = 86400;
        $resource_cache_name = md5($type . $files);
        $resource_cache      = cache($resource_cache_name);
        $last_change_time    = $resource_cache['time'] ? $resource_cache['time'] : \Carbon\Carbon::now()->getTimestamp();

        $request        = request();
        $PageWasUpdated = ($request->hasHeader('If-Modified-Since') and strtotime($request->header('If-Modified-Since')) == $last_change_time);
        $DoIDsMatch     = ($request->hasHeader('If-None-Match') and $request->header('If-None-Match') == $resource_cache_name);
        $PageWasUpdated = $DoIDsMatch = false;

        if ($PageWasUpdated or $DoIDsMatch) {
            return response('', 304)
                ->header('ETag', $resource_cache_name)
                ->header('Connection', 'close');
        } else {
            $content = '';
            if ($resource_cache) {
                $content = $resource_cache['content'];
            } else {
                $filesystem = new \Illuminate\Filesystem\Filesystem();
                foreach (explode(',', $files) as $file) {
                    if (false !== strrpos($file, '..')) {
                        continue;
                    }
                    $file_path     = public_path($type . '/' . $file . '.' . $type);
                    $cache_name    = 'Minify_' . md5($file_path);
                    $cache_content = cache($cache_name);
                    if (!$cache_content) {
                        $file_content  = $filesystem->get($file_path);
                        $cache_content = $Min->minify($file_content);
                        cache([$cache_name => $cache_content], 1 / 60);
                    }
                    $content .= $cache_content;
                }
                cache([$resource_cache_name => ['content' => $content, 'time' => $last_change_time]], 1 / 60);

            }
            return response($content, 200)
                ->header('Content-Type', $content_type)
                ->header('Content-Length', strlen($content))
                ->header('ETag', $resource_cache_name)
                ->header('Cache-Control', ' max-age=' . $cache_time)
                ->header('Expires', gmdate("D, d M Y H:i:s", time() + 31536000) . " GMT")
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', $last_change_time) . ' GMT');
        }
    }
}