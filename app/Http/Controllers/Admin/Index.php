<?php
// 后台 默认主页

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

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
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'site_title',
                'site_domain',
                'site_keywords',
                'site_description',
                'site_other',
                'site_company',
                'site_phone',
                'site_telphone',
                'site_addr',
                'site_icpnumber',
                'site_script',
            ];
            return $this->_put_config($col, 'website');
        }

        $assign['title'] = trans('common.website') . trans('common.config');
        return view('admin.Index_websiteSet', $assign);
    }

    //系统基本设置
    public function systemSet()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'sys_date',
                'sys_date_detail',
                'sys_max_row',
                'sys_max_page',
                'sys_td_cache',
                'data_cache_time', //修改核心系统的数据缓存时间
                'sys_default_image',
                'sys_sync_image',
            ];
            return $this->_put_config($col, 'system');
        }

        $assign['title'] = trans('common.system') . trans('common.config');
        return view('admin.Index_systemSet', $assign);
    }

    //网站数据库配置设置
    public function databaseSet()
    {
        //备份数据库
        if (request()->isMethod('GET') && '1' == request('backup')) {
            $tables           = DB::select('SHOW TABLES');
            $backStr          = '';
            $isMagicQuotesGpc = get_magic_quotes_gpc();
            foreach ($tables as $table) {
                $keyName   = key($table);
                $tableName = $table->$keyName;
                $backStr .= "DROP TABLE IF EXISTS `$tableName` ;\n";
                $createTable = DB::select('SHOW CREATE TABLE`' . $tableName . '`');
                $objectKey   = 'Create Table';
                $createTable = array_pop($createTable)->$objectKey;
                $backStr .= str_replace("\n", "", $createTable) . " ;\n";
                $columns      = DB::select('SHOW COLUMNS FROM `' . $tableName . '`');
                $insertColumn = '';
                foreach ($columns as $column) {
                    if ($insertColumn) {
                        $insertColumn .= ',';
                    }
                    $insertColumn .= '`' . $column->Field . '`';
                }
                $values = DB::select('SELECT * FROM `' . $tableName . '`');
                foreach ($values as $val) {
                    $insertVal = '';
                    $insertV   = '';
                    foreach ($val as $v) {
                        if ($insertV) {
                            $insertV .= ',';
                        }
                        if (is_int($v)) {
                            $insertV .= $v;
                        } elseif (is_null($v)) {
                            $insertV .= 'NULL';
                        } else {
                            $insertV .= ($isMagicQuotesGpc) ? "'$v'" : "'" . addslashes($v) . "'";
                        }
                    }
                    $insertVal .= "($insertV)";
                    if ($insertVal) {
                        $insertVal = str_replace(["\n", "\r"], ["", ""], $insertVal);
                        $backStr .= "INSERT INTO `$tableName`($insertColumn) VALUES $insertVal;\n";
                    }
                }
            }
            return response($backStr)
                ->header('Content-type', 'application/octet-stream')
                ->header('Accept-Ranges', 'bytes')
                ->header('Accept-Length', strlen($backStr))
                ->header('Content-Disposition', 'attachment; filename=' . 'backup' . date('YmdHis') . '.sql');
        }

        //还原数据库
        if (request()->isMethod('POST') && request()->hasFile('restore_file') && request()->file('restore_file')->isValid()) {
            $fileHandle = fopen($_FILES['restore_file']['tmp_name'], 'r');
            if (!$fileHandle) {
                return $this->error(trans('common.open') . trans('common.file') . trans('common.error'));
            }

            while (false !== ($queryStr = fgets($fileHandle, 10240))) {
                $pregMatch = '/^' . implode('|', [
                        'DROP\sTABLE',
                        'INSERT\sINTO',
                        'CREATE\sTABLE',
                    ]) . '/i';
                if (!preg_match($pregMatch, $queryStr)) {
                    return $this->error(trans('common.restore') . trans('common.database') . ' SQL ' . trans('common.error'));
                }
                try {
                    DB::select($queryStr);
                } catch (\Exception $e) {
                    return $this->error(trans('common.restore') . trans('common.database') . trans('common.error') . $e->getMessage());

                }
            }
            fclose($fileHandle);
            return $this->success(trans('common.restore') . trans('common.database') . trans('common.success'));
        }

        //保存数据库配置
        if (request()->isMethod('POST')) {
            $new_config = [
                'DB_HOST'     => request('db_host'),
                'DB_PORT'     => request('db_port'),
                'DB_DATABASE' => request('db_database'),
                'DB_USERNAME' => request('db_username'),
                'DB_PREFIX'   => request('db_prefix'),
            ];
            if (request('edit_password')) {
                $new_config['DB_PASSWORD'] = request('db_password');
            }
            $result     = mPutenv($new_config);
            $result_msg = trans('common.save') . trans('common.database') . trans('common.config');
            if ($result) {
                return $this->success($result_msg . trans('common.success'));
            } else {
                return $this->error($result_msg . trans('common.error'));
            }
        }

        $assign['title'] = trans('common.database') . trans('common.config');
        return view('admin.Index_databaseSet', $assign);
    }

    //修改自己的密码
    public function editMyPass()
    {
        if (request()->isMethod('POST')) {
            $curPassword = request('cur_password');
            $adminInfo = Model\Admin::authorized(session('backend_info.admin_name'), $curPassword);
            if (!$adminInfo) {
                return $this->error(trans('common.current') . trans('common.pass') . trans('common.error'),
                    route('Admin::Index::editMyPass'));
            }

            $password             = request('password');
            $passwordConfirmation = request('password_confirmation');

            //只有一个分支的提交 不进行判断必须检测
            $result = $this->doValidateForm('password', ['password' => $password, 'is_pwd' => true]);
            if (!$result['status']) {
                return $this->error($result['info'], route('Admin::Index::editMyPass'));
            }

            $result            = $this->doValidateForm('password_confirmation', [
                'password'              => $password,
                'password_confirmation' => $passwordConfirmation,
                'is_pwd'                => true,
            ]);
            if (!$result['status']) {
                return $this->error($result['info'], route('Admin::Index::editMyPass'));
            }

            $data               = [];
            $data['admin_pwd'] = $password;
            $resultEdit = Model\Admin::colWhere(session('backend_info.id'))->first()->update($data);
            if ($resultEdit) {
                return $this->success(trans('common.edit') . trans('common.pass') . trans('common.success'),
                    route('Admin::Index::editMyPass'));
            } else {
                return $this->error(trans('common.edit') . trans('common.pass') . trans('common.error'),
                    route('Admin::Index::editMyPass'));
            }
        }
        $assign['title'] = trans('common.edit') . trans('common.pass');
        return view('admin.Index_editMyPass', $assign);
    }

    //清除缓存
    public function cleanCache()
    {
        $messageStr = trans('common.cache') . trans('common.file') . trans('common.and') . trans('common.temp') . trans('common.file');
        $msg        = trans('common.yes') . trans('common.no') . trans('common.confirm') . trans('common.clean') . $messageStr;
        if (!$this->showConfirm($msg)) {
            return;
        }

        try {
            $exitCode = Artisan::call('cache:clear');
        } catch (\Exception $e) {
            $exitCode = $e->getMessage();
        }

        if (0 === $exitCode) {
            //写入日志
            Model\AdminLog::record(session('backend_info.id'));
            return $this->success($messageStr . trans('common.clean') . trans('common.success'),
                route('Admin::Index::main'));
        } else {
            return $this->error($messageStr . trans('common.clean') . trans('common.error'),
                route('Admin::Index::main') . $exitCode);
        }
    }

    //清除日志
    public function cleanLog()
    {
        $msg = trans('common.yes') . trans('common.no') . trans('common.confirm') . trans('common.clean') . trans('common.log');
        if (!$this->showConfirm($msg)) {
            return;
        }

        $filesystem  = new Filesystem();
        $files       = $filesystem->files(storage_path('logs'));
        $cleanResult = $filesystem->delete($files);
        if ($cleanResult) {
            //写入日志
            Model\AdminLog::record(session('backend_info.id'));
            return $this->success(trans('common.clean') . trans('common.log') . trans('common.success'),
                route('Admin::Index::main'));
        } else {
            return $this->error(trans('common.clean') . trans('common.log') . trans('common.error'),
                route('Admin::Index::main'));
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
        $installMenu    = mGetArr(storage_path('app/install_privilege.php'))['Admin'];
        foreach ($installMenu as $groupName => $controllers) {
            foreach ($controllers as $controllerName => $actions) {
                foreach ($actions as $actionName => $actionDescription) {
                    if (
                        //跳过系统基本操作
                        preg_match('/.*::(add|edit|del)$/', $actionName) ||
                        //跳过没有权限的功能
                        (
                            !in_array('all', $adminPriv) &&
                            !in_array($actionName, $adminPriv) &&
                            !in_array('all', $adminGroupPriv) &&
                            !in_array($actionName, $adminGroupPriv)
                        )
                    ) {
                        unset($installMenu[$groupName][$controllerName][$actionName]);
                    }
                }
            }
        }
        $assign['installMenu'] = $installMenu;
        $assign['title']       = trans('common.nav_left') . trans('common.nav');
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
        $siteInfo['sys_timezone']    = config('app.timezone');
        $siteInfo['mysql_version']   = DB::select('select version() as version')[0]->version;
        $siteInfo['ico']             = [
            'ico1'  => $this->_check_privilege('Admin::Article::add'),
            'ico2'  => $this->_check_privilege('Admin::ArticleCategory::add'),
            'ico6'  => $this->_check_privilege('Admin::Member::add'),
            'ico7'  => $this->_check_privilege('Admin::MemberGroup::add'),
            'ico8'  => $this->_check_privilege('Admin::ManageUpload::index'),
            'ico9'  => $this->_check_privilege('Admin::Index::clean_log'),
            'ico10' => $this->_check_privilege('Admin::Index::clean_cache'),
            'ico12' => $this->_check_privilege('Admin::ManageUpload::index'),
        ];
        $assign['site_info']         = $siteInfo;
        $assign['title']             = trans('common.info') . trans('common.page');
        return view('admin.Index_main', $assign);
    }

    public function login()
    {
        $adminName = request('user');
        $adminPwd  = request('pwd');
        switch ($this->doLogin($adminName, $adminPwd)) {
            case 'user_pwd_error':
                return $this->error(trans('common.account') . trans('common.or') . trans('common.pass') . trans('common.error'),
                    route('Admin::Index::index'));
                break;
            case 'verify_error':
                return $this->error(trans('common.verify_code') . trans('common.error'), route('Admin::Index::index'));
                break;
            case 'lock_user_error':
                return $this->error(trans('common.admin') . trans('common.by') . trans('common.lock') . trans('common.please') . config('system.sys_backend_lock_time') . trans('common.second') . trans('common.again') . trans('common.login'),
                    'Admin::Index::index');
                break;
            default:
                return $this->success(trans('common.login') . trans('common.success'), route('Admin::Index::index'));
        }
    }

    //登出
    public function logout()
    {
        $this->doLogout();
        return $this->success(trans('common.logout') . trans('common.account') . trans('common.success'),
            route('Admin::Index::index'));
    }

    //页面验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'password':
                $validator = Validator::make($data, [
                    'password' => 'password:' . true,
                ]);
                break;
            case 'password_confirmation':
                $validator = Validator::make($data, [
                    'password' => 'confirmed',
                ]);
                break;
        }

        if (isset($validator) && $validator->fails()) {
            $result['info'] = implode('', $validator->errors()->all());
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
