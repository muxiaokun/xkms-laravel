<?php
//上传控制器
//kindeditor上传文件接口(全站上传也将使用kindedior插件)
//Upload 目录为基准目录

namespace App\Http\Controllers;

use App\Model;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;

class Upload extends Controller
{
    public function UploadFile(Request $request)
    {
        if (!$request->hasFile('imgFile')) {
            return $this->kind_json('file not exists!');
        }

        if (!$request->file('imgFile')->isValid()) {

            return $this->kind_json($request->file('imgFile')->getErrorMessage());
        }

        $getPathExts = $this->get_path_exts();

        $path = $request->imgFile->store($getPathExts['path'], 'public');
        //大文件上传
        set_time_limit(0);
        // 上传文件的信息写入数据库
        $userType = request('user_type');
        $userId   = $userType ? session('backend_info.id') : session('frontend_info.id');
        if (!$userId) {
            return $this->kind_json('No permission!');
        }


        $data = [
            'name'      => $request->file('imgFile')->getClientOriginalName(),
            'user_id'   => $userId,
            'user_type' => $userType,
            'path'      => $path,
            'mime'      => $request->file('imgFile')->getMimeType(),
            'size'      => $request->file('imgFile')->getSize(),
            'suffix'    => $request->file('imgFile')->getClientOriginalExtension(),
        ];
        Model\ManageUpload::create($data);
        $filesystem   = new Filesystem();
        $storage_path = asset('storage/' . $getPathExts['path'] . '/' . $filesystem->basename($path));
        return $this->kind_json($storage_path, false);
    }

    //kindeditor文件管理器接口
    public function ManageFile()
    {
        $getPathExts = $this->get_path_exts();
        if (!$getPathExts) {
            return;
        }

        $filesystem = new Filesystem();
        $rootUrl    = asset('storage') . '/';
        $rootPath   = storage_path('app/public/');
        //目录名
        $path = $getPathExts['path'];
        if ($path !== '') {
            $rootPath .= $path . '/';
            $rootUrl .= $path . '/';
        }
        //根据path参数，设置各路径和URL
        $getPath            = request('path');
        if ($getPath) {
            $currentPath    = $rootPath;
            $currentUrl     = $rootUrl;
            $currentDirPath = $getPath;
            $moveupDirPath  = preg_replace('/(.*?)[^\/]+\/$/', '$1', $currentDirPath);
        } else {
            $currentPath    = $rootPath;
            $currentUrl     = $rootUrl;
            $currentDirPath = '';
            $moveupDirPath  = '';
        }
        $order = request('order') ? request('order') : 'name';

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $currentPath)) {
            return 'Access is not allowed.';
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $currentPath)) {
            return 'Parameter is not valid.';
        }
        //目录不存在或不是目录
        if (!$filesystem->isDirectory($currentPath)) {
            return 'Directory does not exist.';
        }

        //图片扩展名
        $extArr = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];
        //遍历目录取得文件信息
        $fileList = $filesystem->files($currentPath);

        $result = [];
        foreach ($fileList as $key => $file) {
            if ($filesystem->isDirectory($file)) {
                $result['file_list'][$key]['is_dir']   = true;
                $result['file_list'][$key]['has_file'] = ($filesystem->files($currentPath)) ? true : false;
                $result['file_list'][$key]['filesize'] = 0;
                $result['file_list'][$key]['is_photo'] = false;
                $result['file_list'][$key]['filetype'] = '';
            } elseif ($filesystem->isFile($file)) {
                $result['file_list'][$key]['is_dir']   = false;
                $result['file_list'][$key]['has_file'] = false;
                $result['file_list'][$key]['filesize'] = $filesystem->size($file);
                $fileExt                               = $filesystem->extension($file);
                $result['file_list'][$key]['is_photo'] = in_array($fileExt, $extArr);
                $result['file_list'][$key]['filetype'] = $fileExt;

            }
            $result['file_list'][$key]['filename'] = $filesystem->basename($file); //文件名，包含扩展名
            $result['file_list'][$key]['datetime'] = date('Y-m-d H:i:s', $filesystem->lastModified($file)); //文件最后修改时间
        }

        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveupDirPath;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $currentDirPath;
        //当前目录的URL 伪静态目录结构__ROOT__ . '/' .
        $result['current_url'] = $currentUrl;
        //文件数
        $result['total_count'] = count($fileList);
        //文件列表数组

        return $result;
    }

    private function kind_json($msg, $isError = true)
    {
        if ($isError) {
            return ['error' => 1, 'message' => $msg];
        } else {
            return ['error' => 0, 'url' => $msg];
        }
    }

    private function get_path_exts()
    {
        $dir      = request('dir');
        $allowArr = [
            'image' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
            'flash' => ['swf', 'flv'],
            'media' => ['swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
            'file'  => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
        ];

        if (isset($allowArr[$dir])) {
            $exts = $allowArr[$dir];
        } else {
            false;
        }

        switch (request('t')) {
            case 'kindeditor':
                $path = 'attached';
                break;
            default:
                $path = 'other';
        }
        if ($dir) {
            $path .= '/' . $dir;
        }


        return ['path' => $path, 'exts' => $exts, 'dir' => $dir,];
    }
}
