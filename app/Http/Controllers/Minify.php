<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use GrahamCampbell\HTMLMin\Facades\HTMLMin;

class Minify extends Controller
{
    public function run($type)
    {

        switch ($type) {
            case 'css':
                $Minifier    = HTMLMin::getCssMinifier();
                $contentType = 'text/css;charset=utf-8';
                break;
            case 'js':
                $Minifier    = HTMLMin::getJsMinifier();
                $contentType = 'application/x-javascript;charset=utf-8';
                break;
            default:
                return 'type cannot be empty!';
        }

        $content = '';
        $files   = request('files');
        if (!$files) {
            return 'files cannot be empty';
        }
        $lang    = request('lang');
        $refresh = request('refresh');


        $filesystem = new \Illuminate\Filesystem\Filesystem();

        $cacheTime         = config('system.minify_cache_time');
        $resourceCacheName = md5('Minify_' . $type . '|' . $files . '|' . $lang);
        $resourceCache     = cache($resourceCacheName);
        $currentTime       = Carbon::now()->getTimestamp();
        $lastModifiedTime  = $resourceCache['lastModified'] ? $resourceCache['lastModified'] : $currentTime;

        $request        = request();
        $pageWasUpdated = ($request->hasHeader('If-Modified-Since') && strtotime($request->header('If-Modified-Since')) == $lastModifiedTime);
        $etagMatch      = ($request->hasHeader('If-None-Match') && $request->header('If-None-Match') == $resourceCacheName);

        //test file change
        $filesModified = [];
        foreach (explode(',', $files) as $file) {
            //防止使用父级目录
            if (false !== strrpos($file, '..')) {
                return 'Parent directory is not allowed!';
            }
            //检测文件修改时间
            $filePath              = public_path($type . '/' . $file . '.' . $type);
            $cacheLastModified     = $this->getCacheTimeName($filePath);
            $cacheLastModifiedTime = cache($cacheLastModified);
            $fileLastModified      = $filesystem->lastModified($filePath);
            if ($cacheLastModifiedTime && $cacheLastModifiedTime !== $fileLastModified) {
                $cacheName       = $this->getCacheName($filePath);
                $filesModified[] = $cacheName;
            }
        }

        //debug remove test cache
        if (config('app.debug') && $etagMatch && !$resourceCache && $filesModified) {
            $pageWasUpdated = $etagMatch = false;
        }

        if ($pageWasUpdated && $etagMatch && !$filesModified && !$refresh) {
            return response('', 304);
        } else {
            if (!$resourceCache['lastModified'] || $filesModified || $refresh) {

                if ('js' == $type) {
                    //js类型引入语言包
                    $content .= $this->getJSLang($lang);
                }
                foreach (explode(',', $files) as $file) {
                    //压缩和缓存文件
                    $filePath  = public_path($type . '/' . $file . '.' . $type);
                    $cacheName = $this->getCacheName($filePath);
                    if (false === strrpos($file, '.min')) {
                        $cacheContent = cache($cacheName);
                        if (!$cacheContent || in_array($cacheName, $filesModified) || $refresh) {
                            $fileContent   = $filesystem->get($filePath);
                            $minifyContent = $Minifier->render($fileContent);
                            $cacheContent  = $this->getReplace($type, $file, $minifyContent);
                            cache([$cacheName => $cacheContent], $cacheTime / 60);
                        }
                    } else {
                        $fileContent  = $filesystem->get($filePath);
                        $cacheContent = $this->getReplace($type, $file, $fileContent);
                    }

                    //缓存文件修改时间
                    $fileLastModified  = $filesystem->lastModified($filePath);
                    $cacheLastModified = $this->getCacheTimeName($filePath);
                    cache([$cacheLastModified => $fileLastModified], $cacheTime / 60);


                    $content .= $cacheContent;
                }
                //强制刷新缓存
                if ($refresh) {
                    $lastModifiedTime = 0;
                }
                cache([$resourceCacheName => ['content' => $content, 'lastModified' => $lastModifiedTime]],
                    $cacheTime / 60);
            } else {
                $content = $resourceCache['content'];
            }
            return response($content, 200)
                ->header('Content-Type', $contentType)
                ->header('Content-Length', strlen($content))
                ->header('ETag', $resourceCacheName)
                ->header('Cache-Control', ' max-age=' . $cacheTime)
                ->header('Expires', gmdate("D, d M Y H:i:s", $currentTime + $cacheTime) . " GMT")
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', $lastModifiedTime) . ' GMT');
        }
    }

    protected function getReplace($type, $file, $content)
    {
        switch ($type) {
            case 'js':
                //js 变量替换
                $content = preg_replace_callback('/asset\((.*?)\)/',
                    function ($match) use ($type, $file) {
                        return asset($type . '/' . $match[1]);
                    }, $content);
                break;
            case 'css':
                //css url替换
                $content = preg_replace_callback('/(url\()([\'"])(?!data:image)([^\'"]*?)\2(\))/i',
                    function ($match) use ($type, $file) {
                        return $match[1] . $match[2] . asset($type . '/' . $match[3]) . $match[2] . $match[4];
                    }, $content);
                break;
        }
        return $content;
    }

    protected function getJSLang($lang)
    {
        $langArr = [];
        foreach (explode(',', $lang) as $lang_file) {
            $langArr[$lang_file] = trans($lang_file);
        }
        return $langArr ? 'if(!lang)var lang = {}; Object.assign(lang,' . json_encode($langArr) . ');' : '';
    }

    protected function getCacheName($filePath)
    {
        return md5('Minify_' . $filePath);
    }

    protected function getCacheTimeName($filePath)
    {
        return md5('MinifyLastModified_' . $filePath);
    }
}