<?php
// 前台 会员

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Member extends FrontendMember
{
    public function index()
    {
        if (!$this->isLogin()) {
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

        $memberName = I('user');
        $memberPwd  = I('pwd');
        switch ($this->doLogin($memberName, $memberPwd)) {
            case 'user_pwd_error':
                $this->error(trans('account') . L('or') . L('pass') . L('error'), route('Member/index'));
                break;
            case 'verify_error':
                $this->error(trans('verify_code') . L('error'), route('Member/index'));
                break;
            case 'lock_user_error':
                $this->error(trans('admin') . L('by') . L('lock') . L('please') . config('SYS_FRONTEND_LOCK_TIME') . L('second') . L('again') . L('login'), route('index'));
                break;
            default:
                $this->success(trans('login') . L('success'), route('index'));
        }
    }

    //注册
    public function register()
    {
        if (IS_POST) {
            if (!$this->verifyCheck(I('verify'), 'register') && config('SYS_FRONTEND_VERIFY')) {
                $this->error(trans('verify_code') . L('error'), route('index', array('t' => 'register')));
            }
            $memberName      = I('re_member_name');
            $memberPwd       = I('password');
            $memberPwdAgain = I('password_again');

            //检测初始化参数是否合法
            if ('add' == ACTION_NAME || null !== $memberName) {
                $result = $this->doValidateForm('re_member_name', array('re_member_name' => $memberName));
                if (!$result['status']) {
                    $this->error($result['info'], route('index', array('t' => 'register')));
                }

            }
            if ('add' == ACTION_NAME || null !== $memberPwd) {
                $result = $this->doValidateForm('password', array('password' => $memberPwd));
                if (!$result['status']) {
                    $this->error($result['info'], route('index', array('t' => 'register')));
                }

            }
            if ('add' == ACTION_NAME || null !== $memberPwdAgain) {
                $result = $this->doValidateForm('password_again', array('password' => $memberPwd, 'password_again' => $memberPwdAgain));
                if (!$result['status']) {
                    $this->error($result['info'], route('index', array('t' => 'register')));
                }

            }

            //是否自动启用
            $isEnable = config('SYS_MEMBER_AUTO_ENABLE') ? 1 : 0;
            $data      = array(
                'member_name' => $memberName,
                'member_pwd'  => $memberPwd,
                'is_enable'   => $isEnable,
            );
            $MemberModel = D('Member');
            $addResult  = $MemberModel->mAdd($data);
            if ($addResult) {
                $this->doLogin($memberName, $memberPwd, false);
                $this->success(trans('member') . L('register') . L('success'), route('index'));
                return;
            } else {
                $this->error(trans('member') . L('register') . L('error'), route('index', array('t' => 'register')));
            }
        }
    }

    //登出
    public function logout()
    {
        $this->doLogout();
        $this->success(trans('logout') . L('account') . L('success'), route('Index/index'));
    }

    //表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'user':
                //不能为空
                if ('' == $data['user']) {
                    $result['info'] = trans('member') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名是否存在
                $MemberModel = D('Member');
                $memberInfo = $MemberModel->mSelect(array('member_name' => $data['user']));
                if (0 >= count($memberInfo)) {
                    $result['info'] = trans('member') . L('name') . L('dont') . L('exists');
                    break;
                }
                break;
            case 'password':
                //不能为空
                if ('' == $data['password']) {
                    $result['info'] = trans('pass') . L('not') . L('empty');
                    break;
                }
                //密码长度不能小于6
                if (6 > strlen($data['password'])) {
                    $result['info'] = trans('pass_len_error');
                    break;
                }
                break;
            case 'password_again':
                //检测再一次输入的密码是否一致
                if ($data['password'] != $data['password_again']) {
                    $result['info'] = trans('password_again_error');
                    break;
                }
                //不能为空
                if ('' == $data['password_again']) {
                    $result['info'] = trans('pass') . L('not') . L('empty');
                    break;
                }
                break;
            case 're_member_name':
                //不能为空
                if ('' == $data['re_member_name']) {
                    $result['info'] = trans('member') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['re_member_name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['re_member_name']);
                }

                preg_match('/^[\x80-\xff|a-z|A-Z|0-9]+([^\x80-\xff|^a-z|^A-Z|^0-9]).*$/', $data['re_member_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('name_format_error') . $matches[1];
                    break;
                }
                //检查用户名是否存在
                $MemberModel = D('Member');
                $memberInfo = $MemberModel->mSelect(array('member_name' => $data['re_member_name']));
                if (0 < count($memberInfo)) {
                    $result['info'] = trans('member') . L('name') . L('exists');
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
