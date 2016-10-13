<?php
// Fontend Base Controller 前台基础控制器

namespace App\Http\Controllers;

use App\Model;

class Frontend extends Common
{
    public function _initialize()
    {
        parent::_initialize();
//        if ($this->isLogin()) {
//            //登录后Home公共变量赋值区域
//            $assign['login_info'] =  session('frontend_info');
//        }
        //当前位置
//        if (!IS_AJAX && !$_FILES && !in_array(ACTION_NAME, array('ajax_api', 'del'))) {
//            $this->_get_position();
//        }
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
        $privilege = F('privilege');
        $reData    = [];
        //跳过系统基本操作 删 异步接口,
        $allController = [];
        foreach ($privilege['Home'] as $controllerGroup) {
            $controllerGroup && $allController = array_merge($allController, $controllerGroup);
        }
        $rePosition   = [
            [
                'name' => trans('common.homepage'),
                'link' => route(config('DEFAULT_MODULE') . '/' . C('DEFAULT_CONTROLLER') . '/' . C('DEFAULT_ACTION')),
            ],
        ];
        $positionName = $allController[CONTROLLER_NAME][ACTION_NAME];
        if ($positionName) {
            //给title赋默认值
            $assign['title'] = $positionName;
            if (ACTION_NAME != 'index' && $allControl[CONTROLLER_NAME]['index']) {
                $rePosition[] = [
                    'name' => $allControl[CONTROLLER_NAME]['index'],
                    'link' => route(MODULE_NAME . '/' . CONTROLLER_NAME . '/index'),
                ];
            }
            $rePosition[] = [
                'name' => $positionName,
                'link' => false,
            ];
            //给控制器类型的面包屑导航赋默认值
            $assign['position'] = $rePosition;
        }
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allowAjaxApi = ['get_data'];
        if (!in_array(request('type'), $allowAjaxApi)) {
            return;
        }

        return $this->doAjaxApi();
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
            if (0 != $loginInfo['lock_time'] && $loginInfo['lock_time'] > (time() - $lockTime)) {
                Model\Member::data(['lock_time' => time()])->where(['id' => $loginInfo['id']])->save();
                return 'lock_user_error';
            }
        }
        //验证用户名密码
        $memberInfo = Model\Member::authorized($userName, $password, $memberId);
        if ($memberInfo) {
            //会员有组的 验证组是否启用
            if (0 < count($memberInfo['group_id'])) {
                $memberInfo['group_privilege'] = Model\MemberGroup::mFind_privilege($memberInfo['group_id']);
            }
            //重置登录次数
            if (0 != $memberInfo['login_num']) {
                $loginData = ['login_num' => 0, 'lock_time' => 0];
                Model\Member::data($loginData)->where(['id' => $loginInfo['id']])->save();
            }
            $memberInfo['login_time'] = time();
            session('frontend_info', $memberInfo);
            return 'login_success';
        } else {
            //检测前台尝试登陆次数
            if (0 != $loginNum) {
                $loginData              = [];
                $loginData['login_num'] = $loginInfo['login_num'] + 1;
                $loginData['lock_time'] = ($loginNum <= $loginData['login_num']) ? time() : 0;
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
