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
// 前台 会员

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Member extends FrontendMember
{
    public function index()
    {
        if (!$this->_is_login()) {
            $this->display('login');
            return;
        }
        $this->display();
    }

    //登录
    public function login()
    {
        if (!IS_POST && !IS_AJAX) {
            return;
        }

        $member_name = I('user');
        $member_pwd  = I('pwd');
        switch ($this->_login($member_name, $member_pwd)) {
            case 'user_pwd_error':
                $this->error(L('account') . L('or') . L('pass') . L('error'), U('Member/index'));
                break;
            case 'verify_error':
                $this->error(L('verify_code') . L('error'), U('Member/index'));
                break;
            case 'lock_user_error':
                $this->error(L('admin') . L('by') . L('lock') . L('please') . C('SYS_FRONTEND_LOCK_TIME') . L('second') . L('again') . L('login'), U('index'));
                break;
            default:
                $this->success(L('login') . L('success'), U('index'));
        }
    }

    //注册
    public function register()
    {
        if (IS_POST) {
            if (!$this->_verify_check(I('verify'), 'register') && C('SYS_FRONTEND_VERIFY')) {
                $this->error(L('verify_code') . L('error'), U('index', array('t' => 'register')));
            }
            $member_name      = I('re_member_name');
            $member_pwd       = I('password');
            $member_pwd_again = I('password_again');

            //检测初始化参数是否合法
            if ('add' == ACTION_NAME || null !== $member_name) {
                $result = $this->_validform('re_member_name', array('re_member_name' => $member_name));
                if (!$result['status']) {
                    $this->error($result['info'], U('index', array('t' => 'register')));
                }

            }
            if ('add' == ACTION_NAME || null !== $member_pwd) {
                $result = $this->_validform('password', array('password' => $member_pwd));
                if (!$result['status']) {
                    $this->error($result['info'], U('index', array('t' => 'register')));
                }

            }
            if ('add' == ACTION_NAME || null !== $member_pwd_again) {
                $result = $this->_validform('password_again', array('password' => $member_pwd, 'password_again' => $member_pwd_again));
                if (!$result['status']) {
                    $this->error($result['info'], U('index', array('t' => 'register')));
                }

            }

            //是否自动启用
            $is_enable = C('SYS_MEMBER_AUTO_ENABLE') ? 1 : 0;
            $data      = array(
                'member_name' => $member_name,
                'member_pwd'  => $member_pwd,
                'is_enable'   => $is_enable,
            );
            $MemberModel = D('Member');
            $add_result  = $MemberModel->mAdd($data);
            if ($add_result) {
                $this->_login($member_name, $member_pwd, false);
                $this->success(L('member') . L('register') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('member') . L('register') . L('error'), U('index', array('t' => 'register')));
            }
        }
    }

    //登出
    public function logout()
    {
        $this->_logout();
        $this->success(L('logout') . L('account') . L('success'), U('Index/index'));
    }

    //表单数据验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'user':
                //不能为空
                if ('' == $data['user']) {
                    $result['info'] = L('member') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名是否存在
                $MemberModel = D('Member');
                $member_info = $MemberModel->mSelect(array('member_name' => $data['user']));
                if (0 >= count($member_info)) {
                    $result['info'] = L('member') . L('name') . L('dont') . L('exists');
                    break;
                }
                break;
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
            case 're_member_name':
                //不能为空
                if ('' == $data['re_member_name']) {
                    $result['info'] = L('member') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != C('DEFAULT_CHARSET')) {
                    $data['re_member_name'] = iconv(C('DEFAULT_CHARSET'), 'utf-8', $data['re_member_name']);
                }

                preg_match('/^[\x80-\xff|a-z|A-Z|0-9]+([^\x80-\xff|^a-z|^A-Z|^0-9]).*$/', $data['re_member_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = L('name_format_error') . $matches[1];
                    break;
                }
                //检查用户名是否存在
                $MemberModel = D('Member');
                $member_info = $MemberModel->mSelect(array('member_name' => $data['re_member_name']));
                if (0 < count($member_info)) {
                    $result['info'] = L('member') . L('name') . L('exists');
                    break;
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

}
