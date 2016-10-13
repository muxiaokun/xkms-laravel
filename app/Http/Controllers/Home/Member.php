<?php
// 前台 会员

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;
use App\Model;

class Member extends FrontendMember
{
    public function index()
    {
        if (!$this->isLogin()) {
            return view('home.login', $assign);
            return;
        }
        return view('home.', $assign);
    }

    //登录
    public function login()
    {
        if (!IS_POST && !IS_AJAX) {
            return;
        }

        $memberName = request('user');
        $memberPwd  = request('pwd');
        switch ($this->doLogin($memberName, $memberPwd)) {
            case 'user_pwd_error':
                $this->error(trans('common.account') . trans('common.or') . trans('common.pass') . trans('common.error'),
                    route('Member/index'));
                break;
            case 'verify_error':
                $this->error(trans('common.verify_code') . trans('common.error'), route('Member/index'));
                break;
            case 'lock_user_error':
                $this->error(trans('common.admin') . trans('common.by') . trans('common.lock') . trans('common.please') . config('system.sys_frontend_lock_time') . trans('common.second') . trans('common.again') . trans('common.login'),
                    route('index'));
                break;
            default:
                $this->success(trans('common.login') . trans('common.success'), route('index'));
        }
    }

    //注册
    public function register()
    {
        if (IS_POST) {
            if (!$this->verifyCheck(request('verify'), 'register') && config('system.sys_frontend_verify')) {
                $this->error(trans('common.verify_code') . trans('common.error'), route('index', ['t' => 'register']));
            }
            $memberName     = request('re_member_name');
            $memberPwd      = request('password');
            $memberPwdAgain = request('password_again');

            //检测初始化参数是否合法
            if ('add' == ACTION_NAME || null !== $memberName) {
                $result = $this->doValidateForm('re_member_name', ['re_member_name' => $memberName]);
                if (!$result['status']) {
                    $this->error($result['info'], route('index', ['t' => 'register']));
                }

            }
            if ('add' == ACTION_NAME || null !== $memberPwd) {
                $result = $this->doValidateForm('password', ['password' => $memberPwd]);
                if (!$result['status']) {
                    $this->error($result['info'], route('index', ['t' => 'register']));
                }

            }
            if ('add' == ACTION_NAME || null !== $memberPwdAgain) {
                $result = $this->doValidateForm('password_again',
                    ['password' => $memberPwd, 'password_again' => $memberPwdAgain]);
                if (!$result['status']) {
                    $this->error($result['info'], route('index', ['t' => 'register']));
                }

            }

            //是否自动启用
            $isEnable  = config('system.sys_member_auto_enable') ? 1 : 0;
            $data      = [
                'member_name' => $memberName,
                'member_pwd'  => $memberPwd,
                'is_enable'   => $isEnable,
            ];
            $addResult = Model\Member::mAdd($data);
            if ($addResult) {
                $this->doLogin($memberName, $memberPwd, false);
                $this->success(trans('common.member') . trans('common.register') . trans('common.success'),
                    route('index'));
                return;
            } else {
                $this->error(trans('common.member') . trans('common.register') . trans('common.error'),
                    route('index', ['t' => 'register']));
            }
        }
    }

    //登出
    public function logout()
    {
        $this->doLogout();
        $this->success(trans('common.logout') . trans('common.account') . trans('common.success'),
            route('Index/index'));
    }

    //表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'user':
                //不能为空
                if ('' == $data['user']) {
                    $result['info'] = trans('common.member') . trans('common.name') . trans('common.not') . trans('common.empty');
                    break;
                }
                //检查用户名是否存在
                $memberInfo = Model\Member::mSelect(['member_name' => $data['user']]);
                if (0 >= count($memberInfo)) {
                    $result['info'] = trans('common.member') . trans('common.name') . trans('common.dont') . trans('common.exists');
                    break;
                }
                break;
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
            case 're_member_name':
                //不能为空
                if ('' == $data['re_member_name']) {
                    $result['info'] = trans('common.member') . trans('common.name') . trans('common.not') . trans('common.empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['re_member_name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['re_member_name']);
                }

                preg_match('/^[\x80-\xff|a-z|A-Z|0-9]+([^\x80-\xff|^a-z|^A-Z|^0-9]).*$/', $data['re_member_name'],
                    $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('common.name_format_error') . $matches[1];
                    break;
                }
                //检查用户名是否存在
                $memberInfo = Model\Member::mSelect(['member_name' => $data['re_member_name']]);
                if (0 < count($memberInfo)) {
                    $result['info'] = trans('common.member') . trans('common.name') . trans('common.exists');
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
