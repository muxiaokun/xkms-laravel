<?php
// Fontend Base Controller 前台基础控制器

namespace App\Http\Controllers;

use App\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class Frontend extends Common
{
    public function _initialize()
    {
        parent::_initialize();
        //当前位置
        if (request()->isMethod('GET')) {
            $this->_get_position();
        }
    }

    //生成验证码
    public function verifyImg()
    {
        //查看配置是否需要验证码
        if (!config('system.sys_frontend_verify')) {
            return;
        }

        return parent::verifyImg();
    }

    //检查验证码是否正确
    protected function verifyCheck($code, $t = '')
    {
        //查看配置是否需要验证码
        if (!config('system.sys_frontend_verify')) {
            return true;
        }

        return parent::verifyCheck($code, $t);
    }

    //获取当前位置(也就是当前操作方法)
    protected function _get_position()
    {
        $privilege = mGetArr(storage_path('app/install_privilege.php'))['Home'];
        //跳过系统基本操作 删 异步接口,
        $allController = [
            'Home::Index::index' => trans('common.homepage'),
        ];
        foreach ($privilege as $controllerGroup) {
            $controllerGroup && $allController = array_merge($allController, $controllerGroup);
        }
        $rePosition = [
            [
                'name' => trans('common.homepage'),
                'link' => route('Home::Index::index'),
            ],
        ];
        $assign     = [];
        if (!Route::is('Home::Index::index') && isset($allController[Route::currentRouteName()])) {
            //给title赋默认值
            $positionName    = $allController[Route::currentRouteName()];
            $assign['title'] = $positionName;

            $rePosition[] = [
                'name' => $positionName,
                'link' => false,
            ];
            //给控制器类型的面包屑导航赋默认值
        }
        $assign['position'] = $rePosition;
        View::share($assign);
    }

    //登录功能
    /* $ifVc 是在快捷方式登陆时 不检测验证码
     * $memberInfo 是在快捷方式登陆时 提供用户信息（必须是mFind用户）
     *
     */
    protected function doLogin($userName = null, $password = null, $ifVc = true, $memberId = false)
    {
        if ($ifVc && !$this->verifyCheck(request('verify'), 'login')) {
            return 'verify_error';
        }

        //检测前台尝试登陆次数
        $loginNum = config('system.sys_frontend_login_num');
        $lockTime = config('system.sys_frontend_lock_time');
        if (0 != $loginNum) {
            $loginInfo = Model\Member::mFind(Model\Member::mFindId($userName));
            if (0 != $loginInfo['lock_time'] && $loginInfo['lock_time'] > (Carbon::now() - $lockTime)) {
                Model\Member::data(['lock_time' => Carbon::now()])->where(['id' => $loginInfo['id']])->save();
                return 'lock_user_error';
            }
        }
        //验证用户名密码
        $memberInfo = Model\Member::authorized($userName, $password, $memberId);
        if ($memberInfo) {
            //会员有组的 验证组是否启用
            if (0 < count($memberInfo['group_id'])) {
                $memberInfo['group_privilege'] = Model\MemberGroup::mFindPrivilege($memberInfo['group_id']);
            }
            //重置登录次数
            if (0 != $memberInfo['login_num']) {
                $loginData = ['login_num' => 0, 'lock_time' => 0];
                Model\Member::data($loginData)->where(['id' => $loginInfo['id']])->save();
            }
            $memberInfo['login_time'] = Carbon::now();
            session('frontend_info', $memberInfo);
            return 'login_success';
        } else {
            //检测前台尝试登陆次数
            if (0 != $loginNum) {
                $loginData              = [];
                $loginData['login_num'] = $loginInfo['login_num'] + 1;
                $loginData['lock_time'] = ($loginNum <= $loginData['login_num']) ? Carbon::now() : 0;
                Model\Member::data($loginData)->where(['id' => $loginInfo['id']])->save();
            }
            return 'user_pwd_error';
        }
    }

    //登出功能
    protected function doLogout()
    {
        session('frontend_info', null);
    }

    //子类调用的是否登录的接口
    protected function isLogin()
    {
        if (session('frontend_info')) {
            return true;
        } else {
            return false;
        }
    }
}
