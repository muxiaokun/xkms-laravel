<?php
// Fontend Base Controller 前台基础控制器

namespace App\Http\Controllers;

use App\Model;
use Carbon\Carbon;
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
    /**
     * @param null $userName
     * @param null $password
     * @param bool $ifVc 强制不使用验证码
     * @return string
     */
    protected function doLogin($userName = null, $password = null, $ifVc = true, $memberId = 0)
    {
        if ($ifVc && !$this->verifyCheck(request('verify'), 'login')) {
            return 'verify_error';
        }

        //检测前台尝试登陆次数
        $loginNum   = config('system.sys_frontend_login_num');
        $lockTime   = config('system.sys_frontend_lock_time');
        if ($memberId) {
            $memberInfo = Model\Member::colWhere($memberId)->first();
        } else {
            $where      = [
                ['member_name', $userName],
                ['is_enable', '1'],
            ];
            $memberInfo = Model\Member::where($where)->first();
        }
        if (null === $memberInfo) {
            return 'user_pwd_error';
        }
        if ($loginNum && strtotime($memberInfo->lock_time) > Carbon::now()->getTimestamp() - $lockTime) {
            $memberInfo->update(['lock_time' => Carbon::now()]);
            return 'lock_user_error';
        }
        //验证用户名密码
        if ($memberInfo['member_pwd'] == md5($password . $memberInfo['member_rand']) || $memberId) {
            $loginData = [
                'last_time' => Carbon::now(),
                'login_ip'  => request()->ip(),
            ];
            //重置登录次数
            if (0 != $memberInfo['login_num']) {
                $loginData['login_num'] = 0;
                $loginData['lock_time'] = '1970-01-02 00:00:00';
            }
            $memberInfo->update($loginData);

            //会员有组的 验证组是否启用
            $memberInfo['group_privilege'] = Model\MemberGroup::mFindPrivilege($memberInfo['group_id'])->toArray();
            session(['frontend_info' => $memberInfo->toArray()]);
            return 'login_success';
        } else {
            //检测前台尝试登陆次数
            if (0 != $loginNum) {
                $loginData = [
                    'login_num' => $memberInfo['login_num'] + 1,
                    'lock_time' => ($loginNum <= $memberInfo['login_num']) ? Carbon::now() : '1970-01-02 00:00:00',
                ];
                $memberInfo->update($loginData);
            }
            return 'user_pwd_error';
        }
    }

    //登出功能
    protected function doLogout()
    {
        session(['frontend_info' => null]);
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
