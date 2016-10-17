<?php
// 后台 默认主页

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Index extends Backend
{
    //登录 或者 后台页面框架
    public function index()
    {
        if ($this->isLogin()) {
            $assign['title'] = trans('common.backend');
            return view('admin.Index_index', $assign);
        } else {
            $assign['title'] = trans('common.login') . trans('common.backend');
            return view('admin.Index_login', $assign);
        }
    }

    //网站基本设置
    public function websiteSet()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = [
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
            ];
            $this->_put_config($col, 'website');
            return;
        }

        $assign['title'] = trans('common.website') . trans('common.config');
        return view('admin.Index_', $assign);
    }

    //系统基本设置
    public function systemSet()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = [
                'SYS_DATE',
                'SYS_DATE_DETAIL',
                'SYS_MAX_ROW',
                'SYS_MAX_PAGE',
                'SYS_TD_CACHE',
                'DATA_CACHE_TIME', //修改核心系统的数据缓存时间
                'SYS_DEFAULT_IMAGE',
                'SYS_SYNC_IMAGE',
            ];
            $this->_put_config($col, 'system');
            return;
        }

        $assign['title'] = trans('common.system') . trans('common.config');
        return view('admin.Index_', $assign);
    }

    //网站数据库配置设置
    public function databaseSet()
    {
        //备份数据库
        if (IS_GET && '1' == request('backup')) {
            $db               = M();
            $tables           = $db->query('SHOW TABLES');
            $backStr          = '';
            $isMagicQuotesGpc = get_magic_quotes_gpc();
            foreach ($tables as $table) {
                $tableName = $table[key($table)];
                $backStr .= "DROP TABLE IF EXISTS `$tableName` ;\n";
                $createTable = $db->query('SHOW CREATE TABLE `' . $tableName . '`');
                $createTable = array_pop($createTable[0]);
                $backStr .= str_replace("\n", "", $createTable) . " ;\n";
                $columns      = $db->query('SHOW COLUMNS FROM `' . $tableName . '`');
                $insertColumn = '';
                foreach ($columns as $column) {
                    if ($insertColumn) {
                        $insertColumn .= ',';
                    }

                    $insertColumn .= '`' . array_shift($column) . '`';
                }
                $values = $db->query('SELECT * FROM `' . $tableName . '`');
                foreach ($values as $val) {
                    $insertVal = '';
                    $insertV   = '';
                    foreach ($val as $v) {
                        if ($insertV) {
                            $insertV .= ',';
                        }

                        $insertV .= ($isMagicQuotesGpc) ? "'$v'" : "'" . addslashes($v) . "'";
                    }
                    $insertVal .= "($insertV)";
                    if ($insertVal) {
                        $insertVal = str_replace(["\n", "\r"], ["", ""], $insertVal);
                        $backStr .= "INSERT INTO `$tableName`($insertColumn) VALUES$insertVal ;\n";
                    }
                }
            }
            ob_end_clean();
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: " . strlen($backStr));
            Header("Content-Disposition: attachment; filename=" . 'backup' . date('YmdHis') . '.sql');
            echo $backStr;
            return;
        }

        //还原数据库
        if (IS_POST && isset($_FILES['restore_file']) && 0 == $_FILES['restore_file']['error']) {
            $db         = M();
            $fileHandle = fopen($_FILES['restore_file']['tmp_name'], 'r');
            if (!$fileHandle) {
                $this->error(trans('common.open') . trans('common.file') . trans('common.error'), route('databaseSet'));
            }

            while (false !== ($queryStr = fgets($fileHandle, 10240))) {
                $pregMatch = '/^' . implode('|', [
                        'DROP\sTABLE',
                        'INSERT\sINTO',
                        'CREATE\sTABLE',
                    ]) . '/i';
                if (!preg_match($pregMatch, $queryStr)) {
                    $this->error(trans('common.restore') . trans('common.database') . ' SQL ' . trans('common.error'),
                        route('databaseSet'));
                }

                $db->execute($queryStr);
            }
            fclose($fileHandle);
            $this->success(trans('common.restore') . trans('common.database') . trans('common.success'),
                route('databaseSet'));
            return;
        }

        //保存数据库配置
        if (IS_POST) {
            //表单提交的名称
            $col = [
                'DB_HOST',
                'DB_NAME',
                'DB_USER',
                //'DB_PWD',单独拿出来
                'DB_PORT',
                'DB_PREFIX',
            ];
            if (null !== request('DB_PWD')) {
                array_push($database, 'DB_PWD');
            }

            $this->_put_config($col, 'database');
            return;
        }

        $assign['title'] = trans('common.database') . trans('common.config');
        return view('admin.Index_', $assign);
    }

    //修改自己的密码
    public function editMyPass()
    {
        if (IS_POST) {
            $curPassword = request('cur_password');
            $adminInfo   = Model\Admins::authorized(session('backend_info.admin_name'), $curPassword);
            if (!$adminInfo) {
                $this->error(trans('common.current') . trans('common.pass') . trans('common.error'));
            }

            $password      = request('password');
            $passwordAgain = request('password_again');

            //只有一个分支的提交 不进行判断必须检测
            $result = $this->doValidateForm('password', ['password' => $password]);
            if (!$result['status']) {
                $this->error($result['info'], route(ACTION_NAME));
            }

            $result = $this->doValidateForm('password_again',
                ['password' => $password, 'password_again' => $passwordAgain]);
            if (!$result['status']) {
                $this->error($result['info'], route(ACTION_NAME));
            }

            $resultEdit = Model\Admins::mEdit($adminInfo['id'], ['admin_pwd' => $password]);
            if ($resultEdit) {
                $this->success(trans('common.edit') . trans('common.pass') . trans('common.success'),
                    route('edit_my_pass'));
                return;
            } else {
                $this->error(trans('common.edit') . trans('common.pass') . trans('common.error'),
                    route('edit_my_pass'));
            }
        }
        $assign['title'] = trans('common.edit') . trans('common.pass');
        return view('admin.Index_', $assign);
    }

    //清除缓存
    public function clean_cache()
    {
        $messageStr = trans('common.cache') . trans('common.file') . trans('common.and') . trans('common.temp') . trans('common.file');
        $lang       = trans('common.yes') . trans('common.no') . trans('common.confirm') . trans('common.clean') . $messageStr;
        if (!$this->showConfirm($lang)) {
            return;
        }

        $runtimeFile = scandir(RUNTIME_PATH);
        // 清除runtime 文件
        foreach ($runtimeFile as $file) {
            if (preg_match('/\~runtime\.php$/', $file)) {
                unlink(RUNTIME_PATH . $file);
            }
        }
        $cleanCacheResult = $this->cleanDir(CACHE_PATH);
        $cleanTempResult  = $this->cleanDir(TEMP_PATH);
        if ($cleanCacheResult && $cleanTempResult) {
            //写入日志
            Model\AdminLog::mAdd(session('backend_info.id'));
            $this->success($messageStr . trans('common.clean') . trans('common.success'), route('main'));
        } else {
            $this->error($messageStr . trans('common.clean') . trans('common.error'), route('main'));
        }
    }

    //清除日志
    public function clean_log()
    {
        $lang = trans('common.yes') . trans('common.no') . trans('common.confirm') . trans('common.clean') . trans('common.log');
        if (!$this->showConfirm($lang)) {
            return;
        }

        $cleanResult = $this->cleanDir(LOG_PATH);
        if ($cleanResult) {
            //写入日志
            Model\AdminLog::mAdd(session('backend_info.id'));
            $this->success(trans('common.clean') . trans('common.log') . trans('common.success'), route('main'));
        } else {
            $this->error(trans('common.clean') . trans('common.log') . trans('common.error'), route('main'));
        }
    }

    //管理页面TOP NAV
    public function topNav()
    {
        $assign['title'] = trans('common.nav_top') . trans('common.nav');
        return view('admin.Index_topNav', $assign);
    }

    //管理页面LEFT NAV
    public function leftNav()
    {
        //if(没有在权限中找到列表 就显示默认的列表)
        $adminPriv      = session('backend_info.privilege');
        $adminGroupPriv = session('backend_info.group_privilege');
        $privilege      = F('privilege');
        $leftNav        = [];
        //跳过系统基本操作 增删改 异步接口,
        $denyLink = ['add', 'del', 'edit', 'ajax_port'];
        foreach ($privilege['Admin'] as $control => $controlGroup) {
            foreach ($controlGroup as $controlName => $action) {
                foreach ($action as $actionName => $actionValue) {
                    if (
                        //跳过系统基本操作
                        in_array($actionName, $denyLink) ||
                        //跳过没有权限的功能
                        (
                            !in_array('all', $adminPriv) &&
                            !in_array($controlName . '_' . $actionName, $adminPriv) &&
                            !in_array('all', $adminGroupPriv) &&
                            !in_array($controlName . '_' . $actionName, $adminGroupPriv)
                        )
                    ) {
                        continue;
                    }

                    $leftNav[$control][] = [
                        'link' => route('Admin/' . $controlName . '/' . $actionName),
                        'name' => $actionValue,
                    ];
                }
            }
        }
        $assign['left_nav'] = $leftNav;
        $assign['title']    = trans('common.nav_left') . trans('common.nav');
        return view('admin.Index_leftNav', $assign);
    }

    //管理主页面
    public function main()
    {
        $siteInfo                    = [];
        $siteInfo['sys_version']     = PHP_OS;
        $siteInfo['php_version']     = PHP_VERSION;
        $siteInfo['server_ip']       = $_SERVER['SERVER_ADDR'];
        $siteInfo['max_upload_size'] = ini_get('post_max_size');
        $siteInfo['sys_encode']      = config('DEFAULT_CHARSET');
        $siteInfo['sys_timezone']    = config('DEFAULT_TIMEZONE');
        $siteInfo['mysql_version']   = mysql_get_server_info(M()->db()->connect());
        $siteInfo['mysql_encode']    = config('DB_CHARSET');
        $siteInfo['ico']             = [
            'ico1'  => $this->_check_privilege('add', 'Article'),
            'ico2'  => $this->_check_privilege('add', 'ArticleCategory'),
            'ico6'  => $this->_check_privilege('add', 'Member'),
            'ico7'  => $this->_check_privilege('add', 'MemberGroup'),
            'ico8'  => $this->_check_privilege('index', 'ManageUpload'),
            'ico9'  => $this->_check_privilege('clean_log', 'Index'),
            'ico10' => $this->_check_privilege('clean_cache', 'Index'),
            'ico12' => $this->_check_privilege('index', 'ManageUpload'),
        ];
        $assign['site_info']         = $siteInfo;
        $assign['title']             = trans('common.info') . trans('common.page');
        return view('admin.Index_', $assign);
    }

    public function login()
    {
        $adminName = request('user');
        $adminPwd  = request('pwd');
        switch ($this->doLogin($adminName, $adminPwd)) {
            case 'user_pwd_error':
                return $this->error(trans('common.account') . trans('common.or') . trans('common.pass') . trans('common.error'),
                    'Admin::Index::index');
                break;
            case 'verify_error':
                return $this->error(trans('common.verify_code') . trans('common.error'), 'Admin::Index::index');
                break;
            case 'lock_user_error':
                return $this->error(trans('common.admin') . trans('common.by') . trans('common.lock') . trans('common.please') . config('system.sys_backend_lock_time') . trans('common.second') . trans('common.again') . trans('common.login'),
                    'Admin::Index::index');
                break;
            default:
                return $this->success(trans('common.login') . trans('common.success'), 'Admin::Index::index');
        }
    }

    //登出
    public function logout()
    {
        $this->doLogout();
        $this->success(trans('common.logout') . trans('common.account') . trans('common.success'),
            'Admin::Index::index');
    }

    //页面验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'password':
                //不能为空
                if ('' == $data['password']) {
                    $result['info'] = trans('common.pass') . trans('common.not') . trans('common.empty');
                    break;
                }
                //密码长度不能小于6
                if (6 > strlen($data['password'])) {
                    $result['info'] = trans('common.pass_len_error');
                    break;
                }
                break;
            case 'password_again':
                //检测再一次输入的密码是否一致
                if ($data['password'] != $data['password_again']) {
                    $result['info'] = trans('common.password_again_error');
                    break;
                }
                //不能为空
                if ('' == $data['password_again']) {
                    $result['info'] = trans('common.pass') . trans('common.not') . trans('common.empty');
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
    private function cleanDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $dirs = scandir($dir);
        //不删除的默认文件数组
        $denyDel = ['.', '..', 'index.html'];
        foreach ($dirs as $file) {
            if (in_array($file, $denyDel)) {
                continue;
            }

            $newDir = $dir . '/' . $file;
            if (is_dir($newDir)) {
                $this->cleanDir($newDir);
            } else {
                unlink($newDir);
            }
        }
        return true;
    }
}
