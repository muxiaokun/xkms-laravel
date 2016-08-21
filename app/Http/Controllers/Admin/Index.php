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
// 后台 默认主页

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Index extends Backend
{
    //登录 或者 后台页面框架
    public function index()
    {
        if ($this->_is_login()) {
            $this->display();
        } else {
            $this->assign('title', L('login') . L('backend'));
            $this->display('login');
        }
    }

    //网站基本设置
    public function website_set()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'SITE_TITLE',
                'SITE_DOMAIN',
                'SITE_KEYWORDS',
                'SITE_DESCRIPTION',
                'SITE_OTHER',
                'SITE_COMPANY',
                'SITE_PHONE',
                'SITE_TELPHONE',
                'SITE_ADDR',
                'SITE_ICPNUMBER',
                'SITE_SCRIPT',
            );
            $this->_put_config($col, 'website');
            return;
        }

        $this->assign('title', L('website') . L('config'));
        $this->display();
    }

    //系统基本设置
    public function system_set()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'SYS_DATE',
                'SYS_DATE_DETAIL',
                'SYS_MAX_ROW',
                'SYS_MAX_PAGE',
                'SYS_TD_CACHE',
                'DATA_CACHE_TIME', //修改核心系统的数据缓存时间
                'SYS_DEFAULT_IMAGE',
                'SYS_SYNC_IMAGE',
            );
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', L('system') . L('config'));
        $this->display();
    }

    //网站数据库配置设置
    public function database_set()
    {
        //备份数据库
        if (IS_GET && '1' == I('backup')) {
            $db                  = M();
            $tables              = $db->query('SHOW TABLES');
            $back_str            = '';
            $is_magic_quotes_gpc = get_magic_quotes_gpc();
            foreach ($tables as $table) {
                $table_name = $table[key($table)];
                $back_str .= "DROP TABLE IF EXISTS `$table_name` ;\n";
                $create_table = $db->query('SHOW CREATE TABLE `' . $table_name . '`');
                $create_table = array_pop($create_table[0]);
                $back_str .= str_replace("\n", "", $create_table) . " ;\n";
                $columns    = $db->query('SHOW COLUMNS FROM `' . $table_name . '`');
                $insert_col = '';
                foreach ($columns as $column) {
                    if ($insert_col) {
                        $insert_col .= ',';
                    }

                    $insert_col .= '`' . array_shift($column) . '`';
                }
                $values = $db->query('SELECT * FROM `' . $table_name . '`');
                foreach ($values as $val) {
                    $insert_val = '';
                    $insert_v   = '';
                    foreach ($val as $v) {
                        if ($insert_v) {
                            $insert_v .= ',';
                        }

                        $insert_v .= ($is_magic_quotes_gpc) ? "'$v'" : "'" . addslashes($v) . "'";
                    }
                    $insert_val .= "($insert_v)";
                    if ($insert_val) {
                        $insert_val = str_replace(array("\n", "\r"), array("", ""), $insert_val);
                        $back_str .= "INSERT INTO `$table_name`($insert_col) VALUES$insert_val ;\n";
                    }
                }
            }
            ob_end_clean();
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: " . strlen($back_str));
            Header("Content-Disposition: attachment; filename=" . 'backup' . date('YmdHis') . '.sql');
            echo $back_str;
            return;
        }

        //还原数据库
        if (IS_POST && isset($_FILES['restore_file']) && 0 == $_FILES['restore_file']['error']) {
            $db          = M();
            $file_handle = fopen($_FILES['restore_file']['tmp_name'], 'r');
            if (!$file_handle) {
                $this->error(L('open') . L('file') . L('error'), U('database_set'));
            }

            while (false !== ($query_str = fgets($file_handle, 10240))) {
                $preg_match = '/^' . implode('|', array(
                    'DROP\sTABLE',
                    'INSERT\sINTO',
                    'CREATE\sTABLE',
                )) . '/i';
                if (!preg_match($preg_match, $query_str)) {
                    $this->error(L('restore') . L('database') . ' SQL ' . L('error'), U('database_set'));
                }

                $db->execute($query_str);
            }
            fclose($file_handle);
            $this->success(L('restore') . L('database') . L('success'), U('database_set'));
            return;
        }

        //保存数据库配置
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'DB_HOST',
                'DB_NAME',
                'DB_USER',
                //'DB_PWD',单独拿出来
                'DB_PORT',
                'DB_PREFIX',
            );
            if (null !== I('DB_PWD')) {
                array_push($database, 'DB_PWD');
            }

            $this->_put_config($col, 'database');
            return;
        }

        $this->assign('title', L('database') . L('config'));
        $this->display();
    }

    //修改自己的密码
    public function edit_my_pass()
    {
        if (IS_POST) {
            $cur_password = I('cur_password');
            $AdminModel   = D('Admin');
            $admin_info   = $AdminModel->authorized(session('backend_info.admin_name'), $cur_password);
            if (!$admin_info) {
                $this->error(L('current') . L('pass') . L('error'));
            }

            $password       = I('password');
            $password_again = I('password_again');

            //只有一个分支的提交 不进行判断必须检测
            $result = $this->_validform('password', array('password' => $password));
            if (!$result['status']) {
                $this->error($result['info'], U(ACTION_NAME));
            }

            $result = $this->_validform('password_again', array('password' => $password, 'password_again' => $password_again));
            if (!$result['status']) {
                $this->error($result['info'], U(ACTION_NAME));
            }

            $result_edit = $AdminModel->mEdit($admin_info['id'], array('admin_pwd' => $password));
            if ($result_edit) {
                $this->success(L('edit') . L('pass') . L('success'), U('edit_my_pass'));
                return;
            } else {
                $this->error(L('edit') . L('pass') . L('error'), U('edit_my_pass'));
            }
        }
        $this->assign('title', L('edit') . L('pass'));
        $this->display();
    }

    //清除缓存
    public function clean_cache()
    {
        $message_str = L('cache') . L('file') . L('and') . L('temp') . L('file');
        $lang        = L('yes') . L('no') . L('confirm') . L('clean') . $message_str;
        if (!$this->show_confirm($lang)) {
            return;
        }

        $runtime_file = scandir(RUNTIME_PATH);
        // 清除runtime 文件
        foreach ($runtime_file as $file) {
            if (preg_match('/\~runtime\.php$/', $file)) {
                unlink(RUNTIME_PATH . $file);
            }
        }
        $clean_cache_result = $this->_clean_dir(CACHE_PATH);
        $clean_temp_result  = $this->_clean_dir(TEMP_PATH);
        if ($clean_cache_result && $clean_temp_result) {
            //写入日志
            $AdminLogModel = D('AdminLog');
            $AdminLogModel->mAdd(session('backend_info.id'));
            $this->success($message_str . L('clean') . L('success'), U('main'));
        } else {
            $this->error($message_str . L('clean') . L('error'), U('main'));
        }
    }

    //清除日志
    public function clean_log()
    {
        $lang = L('yes') . L('no') . L('confirm') . L('clean') . L('log');
        if (!$this->show_confirm($lang)) {
            return;
        }

        $clean_result = $this->_clean_dir(LOG_PATH);
        if ($clean_result) {
            //写入日志
            $AdminLogModel = D('AdminLog');
            $AdminLogModel->mAdd(session('backend_info.id'));
            $this->success(L('clean') . L('log') . L('success'), U('main'));
        } else {
            $this->error(L('clean') . L('log') . L('error'), U('main'));
        }
    }

    //管理页面TOP NAV
    public function top_nav()
    {
        $this->assign('title', L('nav_top') . L('nav'));
        $this->display();
    }

    //管理页面LEFT NAV
    public function left_nav()
    {
        //if(没有在权限中找到列表 就显示默认的列表)
        $admin_priv       = session('backend_info.privilege');
        $admin_group_priv = session('backend_info.group_privilege');
        $privilege        = F('privilege');
        $left_nav         = array();
        //跳过系统基本操作 增删改 异步接口,
        $deny_link = array('add', 'del', 'edit', 'ajax_port');
        foreach ($privilege['Admin'] as $control => $control_group) {
            foreach ($control_group as $control_name => $action) {
                foreach ($action as $action_name => $action_value) {
                    if (
                        //跳过系统基本操作
                        in_array($action_name, $deny_link) ||
                        //跳过没有权限的功能
                        (
                            !in_array('all', $admin_priv) &&
                            !in_array($control_name . '_' . $action_name, $admin_priv) &&
                            !in_array('all', $admin_group_priv) &&
                            !in_array($control_name . '_' . $action_name, $admin_group_priv)
                        )
                    ) {
                        continue;
                    }

                    $left_nav[$control][] = array(
                        'link' => U('Admin/' . $control_name . '/' . $action_name),
                        'name' => $action_value,
                    );
                }
            }
        }
        $this->assign('left_nav', $left_nav);
        $this->assign('title', L('nav_left') . L('nav'));
        $this->display();
    }

    //管理主页面
    public function main()
    {
        $site_info                    = array();
        $site_info['sys_version']     = PHP_OS;
        $site_info['php_version']     = PHP_VERSION;
        $site_info['server_ip']       = $_SERVER['SERVER_ADDR'];
        $site_info['max_upload_size'] = ini_get('post_max_size');
        $site_info['sys_encode']      = C('DEFAULT_CHARSET');
        $site_info['sys_timezone']    = C('DEFAULT_TIMEZONE');
        $site_info['mysql_version']   = mysql_get_server_info(M()->db()->connect());
        $site_info['mysql_encode']    = C('DB_CHARSET');
        $site_info['ico']             = array(
            'ico1'  => $this->_check_privilege('add', 'Article'),
            'ico2'  => $this->_check_privilege('add', 'ArticleCategory'),
            'ico6'  => $this->_check_privilege('add', 'Member'),
            'ico7'  => $this->_check_privilege('add', 'MemberGroup'),
            'ico8'  => $this->_check_privilege('index', 'ManageUpload'),
            'ico9'  => $this->_check_privilege('clean_log', 'Index'),
            'ico10' => $this->_check_privilege('clean_cache', 'Index'),
            'ico12' => $this->_check_privilege('index', 'ManageUpload'),
        );
        $this->assign('site_info', $site_info);
        $this->assign('title', L('info') . L('page'));
        $this->display();
    }

    public function login()
    {
        if (!IS_POST && !IS_AJAX) {
            return;
        }

        $admin_name = I('user');
        $admin_pwd  = I('pwd');
        switch ($this->_login($admin_name, $admin_pwd)) {
            case 'user_pwd_error':
                $this->error(L('account') . L('or') . L('pass') . L('error'), U('Index/index'));
                break;
            case 'verify_error':
                $this->error(L('verify_code') . L('error'), U('Index/index'));
                break;
            case 'lock_user_error':
                $this->error(L('admin') . L('by') . L('lock') . L('please') . C('SYS_BACKEND_LOCK_TIME') . L('second') . L('again') . L('login'), U('Index/index'));
                break;
            default:
                $this->success(L('login') . L('success'), U('Index/index'));
        }
    }

    //登出
    public function logout()
    {
        $this->_logout();
        $this->success(L('logout') . L('account') . L('success'), U('Index/index'));
    }

    //页面验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'password':
                //不能为空
                if ('' == $data['password']) {
                    $result['info'] = L('pass') . L('not') . L('empty');
                    break;
                }
                //密码长度不能小于6
                if (6 > strlen($data['password'])) {
                    $result['info'] = L('pass_len_error');
                    break;
                }
                break;
            case 'password_again':
                //检测再一次输入的密码是否一致
                if ($data['password'] != $data['password_again']) {
                    $result['info'] = L('password_again_error');
                    break;
                }
                //不能为空
                if ('' == $data['password_again']) {
                    $result['info'] = L('pass') . L('not') . L('empty');
                    break;
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //清除指定目录的文件
    private function _clean_dir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $dirs = scandir($dir);
        //不删除的默认文件数组
        $deny_del = array('.', '..', 'index.html');
        foreach ($dirs as $file) {
            if (in_array($file, $deny_del)) {
                continue;
            }

            $new_dir = $dir . '/' . $file;
            if (is_dir($new_dir)) {
                $this->_clean_dir($new_dir);
            } else {
                unlink($new_dir);
            }
        }
        return true;
    }
}
