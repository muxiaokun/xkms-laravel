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
// Fontend Base Controller 前台基础控制器

namespace App\Http\Controllers;

class Frontend extends Common
{
    public function _initialize()
    {
        parent::_initialize();
        if ($this->_is_login()) {
            //登录后Home公共变量赋值区域
            $this->assign('login_info', session('frontend_info'));
        }
        //当前位置
        if (!IS_AJAX && !$_FILES && !in_array(ACTION_NAME, array('ajax_api', 'del'))) {
            $this->_get_position();
        }
    }

    //生成验证码
    public function verify_img()
    {
        //查看配置是否需要验证码
        if (!C('SYS_FRONTEND_VERIFY')) {
            return;
        }

        return parent::verify_img();
    }

    //检查验证码是否正确
    protected function _verify_check($code, $t = '')
    {
        //查看配置是否需要验证码
        if (!C('SYS_FRONTEND_VERIFY')) {
            return true;
        }

        return parent::_verify_check($code, $t);
    }

    //获取当前位置(也就是当前操作方法)
    protected function _get_position()
    {
        $privilege = F('privilege');
        $re_data   = array();
        //跳过系统基本操作 删 异步接口,
        $all_controller = array();
        foreach ($privilege['Home'] as $controller_group) {
            $controller_group && $all_controller = array_merge($all_controller, $controller_group);
        }
        $re_position = array(array(
            'name' => L('homepage'),
            'link' => U(C('DEFAULT_MODULE') . '/' . C('DEFAULT_CONTROLLER') . '/' . C('DEFAULT_ACTION')),
        ));
        $position_name = $all_controller[CONTROLLER_NAME][ACTION_NAME];
        if ($position_name) {
            //给title赋默认值
            $this->assign('title', $position_name);
            if (ACTION_NAME != 'index' && $all_control[CONTROLLER_NAME]['index']) {
                $re_position[] = array(
                    'name' => $all_control[CONTROLLER_NAME]['index'],
                    'link' => U(MODULE_NAME . '/' . CONTROLLER_NAME . '/index'),
                );
            }
            $re_position[] = array(
                'name' => $position_name,
                'link' => false,
            );
            //给控制器类型的面包屑导航赋默认值
            $this->assign('position', $re_position);
        }
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allow_ajax_api = array('get_data');
        if (!in_array(I('type'), $allow_ajax_api)) {
            return;
        }

        $this->_ajax_api();
    }

    //登录功能
    /* $if_vc 是在快捷方式登陆时 不检测验证码
     * $member_info 是在快捷方式登陆时 提供用户信息（必须是mFind用户）
     *
     */
    protected function _login($user_name = null, $password = null, $if_vc = true, $member_id = false)
    {
        if ($if_vc && !$this->_verify_check(I('verify'), 'login')) {
            return 'verify_error';
        }

        $MemberModel = D('Member');
        //检测前台尝试登陆次数
        $login_num = C('SYS_FRONTEND_LOGIN_NUM');
        $lock_time = C('SYS_FRONTEND_LOCK_TIME');
        if (0 != $login_num) {
            $login_info = $MemberModel->mFind($MemberModel->mFindId($user_name));
            if (0 != $login_info['lock_time'] && $login_info['lock_time'] > (time() - $lock_time)) {
                $MemberModel->data(array('lock_time' => time()))->where(array('id' => $login_info['id']))->save();
                return 'lock_user_error';
            }
        }
        //验证用户名密码
        $member_info = $MemberModel->authorized($user_name, $password, $member_id);
        if ($member_info) {
            //会员有组的 验证组是否启用
            if (0 < count($member_info['group_id'])) {
                $MemberGroupModel               = D('MemberGroup');
                $member_info['group_privilege'] = $MemberGroupModel->mFind_privilege($member_info['group_id']);
            }
            //重置登录次数
            if (0 != $member_info['login_num']) {
                $login_data = array('login_num' => 0, 'lock_time' => 0);
                $MemberModel->data($login_data)->where(array('id' => $login_info['id']))->save();
            }
            $member_info['login_time'] = time();
            session('frontend_info', $member_info);
            return 'login_success';
        } else {
            //检测前台尝试登陆次数
            if (0 != $login_num) {
                $login_data              = array();
                $login_data['login_num'] = $login_info['login_num'] + 1;
                $login_data['lock_time'] = ($login_num <= $login_data['login_num']) ? time() : 0;
                $MemberModel->data($login_data)->where(array('id' => $login_info['id']))->save();
            }
            return 'user_pwd_error';
        }
    }

    //登出功能
    protected function _logout()
    {
        session('frontend_info', null);
    }

    //子类调用的是否登录的接口
    protected function _is_login()
    {
        if (session('frontend_info')) {
            return true;
        } else {
            return false;
        }
    }
}
