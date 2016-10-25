<?php
//后台 管理上传 控制器
//kindeditor上传文件接口(全站上传也将使用kindedior插件)
//Upload 目录为基准目录

namespace App\Http\Controllers;

use App\Model;

class CommonManageUpload
{
    protected $config = [

//        Upload Class base configure
        //        'mimes'         =>  array(), //允许上传的文件MiMe类型
        //        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        //        'exts'          =>  array(), //允许上传的文件后缀
        //        'autoSub'       =>  true, //自动子目录保存文件
        //        'subName'       =>  array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => 'Uploads/', //保存根路径
        //        'savePath'      =>  '', //保存路径
        //        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        //        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        //        'replace'       =>  false, //存在同名是否覆盖
        //        'hash'          =>  true, //是否生成hash编码
        //        'callback'      =>  false, //检测文件是否存在回调，如果存在返回文件信息数组
        //        'driver'        =>  '', // 文件上传驱动
        //        'driverConfig'  =>  array(), // 上传驱动配置
    ];

    public function UploadFile()
    {
        $UploadUtil  = new \Think\Upload($this->config);
        $getPathExts = $this->get_path_exts();
        if (!$getPathExts) {
            return;
        }

        $UploadUtil->savePath = $getPathExts['path'] . $getPathExts['dir_name'];
        $UploadUtil->exts     = $getPathExts['exts'];
        //大文件上传
        set_time_limit(0);
        $fileInfo = $UploadUtil->uploadOne($_FILES['imgFile']);
        if (!$fileInfo) {
            // 上传错误提示错误信息
            $this->kind_json($UploadUtil->getError());
        } else {
            $fileUrl = $UploadUtil->rootPath . $fileInfo['savepath'] . $fileInfo['savename'];
            // 上传文件的信息写入数据库
            $data              = [
                'name'   => substr($fileInfo['name'], 0, strrpos($fileInfo['name'], '.')),
                'path'   => $fileUrl,
                'mime'   => $fileInfo['type'],
                'size'   => $fileInfo['size'],
                'suffix' => $fileInfo['ext'],
            ];
            Model\ManageUpload::mAdd($data);
            // 上传成功 返回文件信息 伪静态目录结构__ROOT__ . '/' .
            $this->kind_json(__ROOT__ . '/' . $fileUrl, false);
        }
    }

    //kindeditor文件管理器接口
    public function ManageFile()
    {
        $getPathExts = $this->get_path_exts();
        if (!$getPathExts) {
            return;
        }

        $UploadUtil = new \Think\Upload($this->config);
        $rootPath   = $UploadUtil->rootPath . $getPathExts['path'];
        //目录名
        $dirName = $getPathExts['dir_name'];
        if ($dirName !== '') {
            $rootPath .= $dirName;
            $rootUrl = $rootPath;
            if (!file_exists($rootPath)) {
                mkdir($rootPath, 0755, true);
            }
        }
        //根据path参数，设置各路径和URL
        $getPath = request('get.path');
        if (!$getPath) {
            $currentPath    = $rootPath;
            $currentUrl     = $rootUrl;
            $currentDirPath = '';
            $moveupDirPath  = '';
        } else {
            $currentPath    = $rootPath . '/' . $getPath;
            $currentUrl     = $rootUrl . $getPath;
            $currentDirPath = $getPath;
            $moveupDirPath  = preg_replace('/(.*?)[^\/]+\/$/', '$1', $currentDirPath);
        }
        $order = request('get.order') ? request('get.order') : 'name';

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $currentPath)) {
            echo 'Access is not allowed.';
            return;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $currentPath)) {
            echo 'Parameter is not valid.';
            return;
        }
        //目录不存在或不是目录
        if (!file_exists($currentPath) || !is_dir($currentPath)) {
            echo 'Directory does not exist.';
            return;
        }

        //图片扩展名
        $extArr = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];
        //遍历目录取得文件信息
        $fileList = [];
        if ($handle = opendir($currentPath)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') {
                    continue;
                }

                $file = $currentPath . $filename;
                if (is_dir($file)) {
                    $fileList[$i]['is_dir']   = true; //是否文件夹
                    $fileList[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $fileList[$i]['filesize'] = 0; //文件大小
                    $fileList[$i]['is_photo'] = false; //是否图片
                    $fileList[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $fileList[$i]['is_dir']   = false;
                    $fileList[$i]['has_file'] = false;
                    $fileList[$i]['filesize'] = filesize($file);
                    $fileList[$i]['dir_path'] = '';
                    $fileExt                  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $fileList[$i]['is_photo'] = in_array($fileExt, $extArr);
                    $fileList[$i]['filetype'] = $fileExt;
                }
                $fileList[$i]['filename'] = $filename; //文件名，包含扩展名
                $fileList[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }
        //匿名函数phpversion>5.3.0
        usort($fileList, function ($a, $b) {
            $order = request('get.order', null, 'strtolower');
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } else {
                if (!$a['is_dir'] && $b['is_dir']) {
                    return 1;
                } else {
                    if ($order == 'size') {
                        if ($a['filesize'] > $b['filesize']) {
                            return 1;
                        } else {
                            if ($a['filesize'] < $b['filesize']) {
                                return -1;
                            } else {
                                return 0;
                            }
                        }
                    } else {
                        if ($order == 'type') {
                            return strcmp($a['filetype'], $b['filetype']);
                        } else {
                            return strcmp($a['filename'], $b['filename']);
                        }
                    }
                }
            }
        });

        $result = [];
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveupDirPath;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $currentDirPath;
        //当前目录的URL 伪静态目录结构__ROOT__ . '/' .
        $result['current_url'] = __ROOT__ . '/' . $currentUrl;
        //文件数
        $result['total_count'] = count($fileList);
        //文件列表数组
        $result['file_list'] = $fileList;

        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
    }

    private function kind_json($msg, $isError = true)
    {
        header('Content-type: text/html; charset=UTF-8');
        if ($isError) {
            echo json_encode(['error' => 1, 'message' => $msg]);
        } else {
            echo json_encode(['error' => 0, 'url' => $msg]);
        }
        return;
    }

    private function get_path_exts()
    {
        $allowArr = [
            'image' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
            'flash' => ['swf', 'flv'],
            'media' => ['swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
            'file'  => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
        ];
        $dirName  = request('get.dir') ? request('get.dir') : '';
        if (empty($allowArr[$dirName]) && '' != $dirName) {
            $this->kind_json("Invalid Directory name.");
            return false;
        }
        if ($dirName) {
            $dirName .= '/';
        }

        switch (request('t')) {
            case 'kindeditor':
                $path = 'attached/';
                break;
            default:
                $path = 'other/';
        }

        return ['path' => $path, 'exts' => $allowArr[$dirName], 'dir_name' => $dirName];
    }
}
