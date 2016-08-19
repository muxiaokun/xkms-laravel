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
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Template extends Backend
{
    //$tpl_info_file 与 Common/Common/function.php M_scan_template一致
    private $tpl_info_file = 'theme_info';
    private $config_file   = '';
    private $default_theme = '';
    private $view_path     = '';
    private $view_files    = array();
    public function _initialize()
    {
        parent::_initialize();
        //初始化模板目录
        $this->view_path = APP_PATH . C('DEFAULT_MODULE') . '/' . C('DEFAULT_V_LAYER') . '/';
        C('DEFAULT_THEME') && $this->view_path .= C('DEFAULT_THEME') . '/';
        //初始化模本文件列表
        $this->view_files = F($this->tpl_info_file, '', $this->view_path);
        if (!$this->view_files) {
            $this->_refresh_view_files();
        }

        //初始化配置文件所在位置
        $this->config_file   = APP_PATH . C('DEFAULT_MODULE') . '/Conf/' . 'config.php';
        $config              = include $this->config_file;
        $this->default_theme = $config['DEFAULT_THEME'];
    }

    //列表
    public function index()
    {
        //刷新模本文件列表
        if (1 == I('refresh')) {
            $this->_refresh_view_files();
            $this->success(L('theme') . L('template') . L('refresh') . L('success'), U('index'));
            return;
        }

        //修改默认主题配置
        $default_theme = I('default_theme');
        if (null !== $default_theme) {
            if ('empty' == $default_theme) {
                $default_theme = '';
            }

            $config['DEFAULT_THEME'] = $default_theme;
            $config_str              = var_export($config, true);
            $Core_Copyright          = C('CORE_COPYRIGHT');
            $put_config              = <<<EOF
<?php
{$Core_Copyright}
// Home 配置
return {$config_str};
?>
EOF;
            file_put_contents($this->config_file, $put_config);
            $this->success(L('theme') . L('selection') . L('success'), U('index'));
            return;
        }

        //修改模板文件信息
        if (IS_POST) {
            foreach ($this->view_files as $file_md5 => $info) {
                $post_info                           = I($file_md5);
                $this->view_files[$file_md5]['name'] = $post_info['name'];
                $this->view_files[$file_md5]['info'] = $post_info['info'];
            }

            //保存缓存
            F($this->tpl_info_file, $this->view_files, $this->view_path);
            $this->success(L('theme') . L('info') . L('save') . L('success'), U('index'));
            return;
        }

        $this->assign('default_theme', $this->default_theme);
        $this->assign('theme_list', $this->_get_theme_list());
        $this->assign('theme_info_list', $this->view_files);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('theme') . L('template') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            //添加时不可以创建新的目录 必须使用系统设置的后缀
            $file_name = trim(I('file_name'));
            //处理2级目录
            $file_path = explode('/', $file_name);
            if (preg_match('/\.\.\//', $file_name)
                || !preg_match('/' . str_ireplace('.', '\.', C('TMPL_TEMPLATE_SUFFIX')) . '$/', $file_name)
            ) {
                $this->error(L('file') . L('name') . L('error'), U('index'));
            }
            if (!$file_name) {
                $this->error(L('file') . L('name') . L('not') . L('empty'), U('index'));
            }

            $file_path = $this->view_path . $file_name;
            $file_md5  = ($file_path);
            if (is_array($this->view_files[$file_md5])) {
                $this->error(L('file') . L('name') . L('repeat'), U('index'));
            }

            $content     = I('content', '', false);
            $result_edit = file_put_contents($file_path, $content);
            if (false !== $result_edit) {
                $this->_refresh_view_files();
                $this->success(L('theme') . L('template') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('theme') . L('template') . L('add') . L('error'), U('index'));
            }
        }

        $this->assign('title', L('add') . L('theme') . L('template') . L('template'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!is_array($this->view_files[$id])) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $file_name = $this->view_files[$id]['file_name'];
        $file_path = $this->view_path . $file_name;
        if (IS_POST) {
            $content     = I('content', '', false);
            $result_edit = file_put_contents($file_path, $content);
            if ($result_edit) {
                $this->success(L('theme') . L('template') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('theme') . L('template') . L('edit') . L('error'), U('index'));
            }
        }

        $edit_info            = $this->view_files[$id];
        $edit_info['content'] = htmlspecialchars(file_get_contents($file_path));
        $this->assign('id', $id);
        $this->assign('edit_info', $edit_info);

        $this->assign('title', L('edit') . L('theme') . L('template') . L('template'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!is_array($this->view_files[$id])) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $file_name  = $this->view_files[$id]['file_name'];
        $file_path  = $this->view_path . $file_name;
        $result_del = unlink($file_path);
        if ($result_del) {
            $this->_refresh_view_files();
            $this->success(L('theme') . L('template') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('theme') . L('template') . L('del') . L('error'), U('index'));
        }
    }

    //获取模板主题
    private function _get_theme_list()
    {
        $theme_list = array();
        if (C('DEFAULT_THEME')) {
            $theme_path = str_ireplace(C('DEFAULT_THEME') . '/', '', $this->view_path);
        }

        $scan_info   = scandir($theme_path);
        $deny_change = array('.', '..', 'index.html');
        foreach ($scan_info as $info) {
            if (in_array($info, $deny_change)) {
                continue;
            }

            if (is_dir($theme_path . $info)) {
                $theme_list[] = $info;
            }
        }
        return $theme_list;
    }

    //刷新模板文件列表
    private function _refresh_view_files()
    {
        //清空缓存
        $this->view_files = array();
        //获取最新的文件信息
        $this->_get_view_files($this->view_path);
        //保存缓存
        F($this->tpl_info_file, $this->view_files, $this->view_path);
    }

    //刷新模板文件列表
    private function _get_view_files($path, $level = 2)
    {
        if (!is_dir($path) || 1 > $level) {
            return false;
        }

        //把文件中的缓存取出
        $theme_info = F($this->tpl_info_file, '', $path);

        //构造模板文件
        //只扫描两级目录不进行递归 跳过 'index.html'
        $scan_info = scandir($path);

        $deny_change = array('.', '..', 'theme_info.php');
        if (2 == $level) {
            array_push($deny_change, 'index.html');
        }

        foreach ($scan_info as $info) {
            if (in_array($info, $deny_change)) {
                continue;
            }

            $new_path = $path . $info;
            if (is_dir($new_path)) {
                $this->_get_view_files($new_path . '/', $level - 1);
            } elseif (is_file($new_path)) {
                $relative_view_path          = str_ireplace($this->view_path, '', $new_path);
                $file_md5                    = md5($relative_view_path);
                $this->view_files[$file_md5] = array(
                    'file_name' => $relative_view_path,
                );
                if ($theme_info && $theme_info[$file_md5]) {
                    $this->view_files[$file_md5]['name'] = $theme_info[$file_md5]['name'];
                    $this->view_files[$file_md5]['info'] = $theme_info[$file_md5]['info'];
                }
            }
        }
    }
}
