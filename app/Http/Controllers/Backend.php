<?php
// Backend Base Controller 后台基础控制器

namespace App\Http\Controllers;

use App\Model;
use Carbon\Carbon;

class Backend extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    //生成验证码
    public function verifyImg()
    {
        //查看配置是否需要验证码
        if (!config('system.sys_backend_verify')) {
            return;
        }

        return parent::verifyImg();
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $t = '')
    {
        //查看配置是否需要验证码
        if (!config('system.sys_backend_verify')) {
            return true;
        }

        return parent::verifyCheck($code, $t);
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allowAjaxApi = ['validform', 'get_data'];
        if (!$this->isLogin() && !in_array(request('type'), $allowAjaxApi)) {
            return;
        }

        return $this->doAjaxApi();
    }

    //登录功能
    public function doLogin($userName, $password)
    {
        if (!$this->verifyCheck(request('verify'))) {
            return 'verify_error';
        }

        //验证用户名
        $where     = [
            ['admin_name', $userName],
            ['is_enable', '1'],
        ];
        $adminInfo = Model\Admin::where($where)->first();
        if (null === $adminInfo) {
            return 'user_pwd_error';
        }
        //检测后台尝试登陆次数
        $loginNum = config('system.sys_backend_login_num');
        $lockTime = config('system.sys_backend_lock_time');
        if ($loginNum && strtotime($adminInfo->lock_time) > Carbon::now()->getTimestamp() - $lockTime) {
            $adminInfo->update(['lock_time' => Carbon::now()]);
            return 'lock_user_error';
        }
        //验证密码
        if ($adminInfo['admin_pwd'] == md5($password . $adminInfo['admin_rand'])) {
            $loginData = [
                'last_time' => Carbon::now(),
                'login_ip'  => request()->ip(),
            ];

            //重置登录次数
            if (0 != $adminInfo['login_num']) {
                $loginData['login_num'] = 0;
                $loginData['lock_time'] = '1970-01-02 00:00:00';
            }

            $adminInfo->update($loginData);

            //管理员有组的 加载分组权限
            $adminInfo['group_privilege'] = Model\AdminGroup::mFindPrivilege($adminInfo['group_id'])->toArray();

            session(['backend_info' => $adminInfo->toArray()]);
            return 'login_success';
        } else {
            //检测后台尝试登陆次数
            if ($loginNum) {
                $loginData = [
                    'login_num' => $adminInfo['login_num'] + 1,
                    'lock_time' => ($loginNum <= $adminInfo['login_num']) ? Carbon::now() : '1970-01-02 00:00:00',
                ];
                $adminInfo->update($loginData);
            }
            return 'user_pwd_error';
        }
    }

    //登出功能
    public function doLogout()
    {
        session(['backend_info' => null]);
    }

    //子类调用的是否登录的接口
    public function isLogin()
    {
        if (session('backend_info')) {
            return true;
        } else {
            return false;
        }
    }

    //调用 404 的默认控制器和默认方法
    public function _empty()
    {
        $EmptyController = A('Common/CommonEmpty');
        $EmptyController->_empty();
    }

    //检测权限
    public function _check_privilege($routeName)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有管理员管理组全部权限 3.拥有权限 4.ajax_api接口 4.不需要权限就能访问的
        $allowRoute     = [
            'Admin::Index::index',
            'Admin::Index::topNav',
            'Admin::Index::leftNav',
            'Admin::Index::main',
            'Admin::Index::login',
            'Admin::Index::logout',
            'Admin::ManageUpload::UploadFile',
            'Admin::ManageUpload::ManageFile',
        ];
        $backendInfo    = session('backend_info');
        $adminPriv      = $backendInfo['privilege'] ? $backendInfo['privilege'] : [];
        $adminGroupPriv = ($backendInfo['group_privilege']) ? $backendInfo['group_privilege'] : [];
        if (
            'ajax_api' != $routeName &&
            !in_array($routeName, $allowRoute) &&
            !in_array('all', $adminPriv) &&
            !in_array($routeName, $adminPriv) &&
            !in_array('all', $adminGroupPriv) &&
            !in_array($routeName, $adminGroupPriv)
        ) {
            return false;
        }

        return true;
    }

    //保存系统配置
    protected function _put_config($col, $file)
    {
        if (!is_array($col) && !in_array($file, ['website', 'system',])) {
            return $this->error(trans('common.save') . trans('common.error'));
        }

        $cfgFile    = config_path($file . '.php');
        $saveConfig = [];
        foreach ($col as $option) {
            $saveConfig[$option] = request($option);
        }
        $putResult = mPutArr($cfgFile, $saveConfig);

        if ($putResult) {
            return $this->success(trans('common.save') . trans('common.success'));
        } else {
            return $this->error(trans('common.save') . trans('common.error'));
        }
    }
}
