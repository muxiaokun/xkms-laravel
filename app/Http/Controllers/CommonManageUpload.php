<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
//后台 管理上传 控制器
//kindeditor上传文件接口(全站上传也将使用kindedior插件)
//Upload 目录为基准目录

namespace App\Http\Controllers;

class CommonManageUploadController
{
    protected $config = array(

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
    );

    public function UploadFile()
    {
        $UploadUtil    = new \Think\Upload($this->config);
        $get_path_exts = $this->get_path_exts();
        if (!$get_path_exts) {
            return;
        }

        $UploadUtil->savePath = $get_path_exts['path'] . $get_path_exts['dir_name'];
        $UploadUtil->exts     = $get_path_exts['exts'];
        //大文件上传
        set_time_limit(0);
        $file_info = $UploadUtil->uploadOne($_FILES['imgFile']);
        if (!$file_info) {
            // 上传错误提示错误信息
            $this->kind_json($UploadUtil->getError());
        } else {
            $file_url = $UploadUtil->rootPath . $file_info['savepath'] . $file_info['savename'];
            // 上传文件的信息写入数据库
            $ManageUploadModel = D('ManageUpload');
            $data              = array(
                'name'   => substr($file_info['name'], 0, strrpos($file_info['name'], '.')),
                'path'   => $file_url,
                'mime'   => $file_info['type'],
                'size'   => $file_info['size'],
                'suffix' => $file_info['ext'],
            );
            $ManageUploadModel->m_add($data);
            // 上传成功 返回文件信息 伪静态目录结构__ROOT__ . '/' .
            $this->kind_json(__ROOT__ . '/' . $file_url, false);
        }
    }
    //kindeditor文件管理器接口
    public function ManageFile()
    {
        $get_path_exts = $this->get_path_exts();
        if (!$get_path_exts) {
            return;
        }

        $UploadUtil = new \Think\Upload($this->config);
        $root_path  = $UploadUtil->rootPath . $get_path_exts['path'];
        //目录名
        $dir_name = $get_path_exts['dir_name'];
        if ($dir_name !== '') {
            $root_path .= $dir_name;
            $root_url = $root_path;
            if (!file_exists($root_path)) {
                mkdir($root_path, 0755, true);
            }
        }
        //根据path参数，设置各路径和URL
        $get_path = I('get.path');
        if (!$get_path) {
            $current_path     = $root_path;
            $current_url      = $root_url;
            $current_dir_path = '';
            $moveup_dir_path  = '';
        } else {
            $current_path     = $root_path . '/' . $get_path;
            $current_url      = $root_url . $get_path;
            $current_dir_path = $get_path;
            $moveup_dir_path  = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        $order = I('get.order') ? I('get.order') : 'name';

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            return;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            return;
        }
        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            return;
        }

        //图片扩展名
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
        //遍历目录取得文件信息
        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') {
                    continue;
                }

                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir']   = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir']   = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext                  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }
        //匿名函数phpversion>5.3.0
        usort($file_list, function ($a, $b) {
            $order = I('get.order', null, 'strtolower');
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } else if (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            } else {
                if ($order == 'size') {
                    if ($a['filesize'] > $b['filesize']) {
                        return 1;
                    } else if ($a['filesize'] < $b['filesize']) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else if ($order == 'type') {
                    return strcmp($a['filetype'], $b['filetype']);
                } else {
                    return strcmp($a['filename'], $b['filename']);
                }
            }
        });

        $result = array();
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL 伪静态目录结构__ROOT__ . '/' .
        $result['current_url'] = __ROOT__ . '/' . $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;

        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
    }

    private function kind_json($msg, $is_error = true)
    {
        header('Content-type: text/html; charset=UTF-8');
        if ($is_error) {
            echo json_encode(array('error' => 1, 'message' => $msg));
        } else {
            echo json_encode(array('error' => 0, 'url' => $msg));
        }
        return;
    }

    private function get_path_exts()
    {
        $allow_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        $dir_name = I('get.dir') ? I('get.dir') : '';
        if (empty($allow_arr[$dir_name]) && '' != $dir_name) {
            $this->kind_json("Invalid Directory name.");
            return false;
        }
        if ($dir_name) {
            $dir_name .= '/';
        }

        switch (I('t')) {
            case 'kindeditor':
                $path = 'attached/';
                break;
            default:
                $path = 'other/';
        }

        return array('path' => $path, 'exts' => $allow_arr[$dir_name], 'dir_name' => $dir_name);
    }
}
