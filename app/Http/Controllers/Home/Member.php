<?php
// 前台 会员

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;
use App\Model;
use Illuminate\Support\Facades\Validator;

class Member extends FrontendMember
{
    public function index()
    {
        if ($this->isLogin()) {
            $this->commonAssgin();
            $assign['title'] = trans('common.member') . trans('common.homepage');
            return view('home.Member_index', $assign);
        } else {
            $assign['title'] = trans('common.login');
            return view('home.Member_login', $assign);
        }
    }

    //登录
    public function login()
    {
        $memberName = request('member_name');
        $memberPwd  = request('password');
        switch ($this->doLogin($memberName, $memberPwd)) {
            case 'user_pwd_error':
                return $this->error(trans('common.account') . trans('common.or') . trans('common.pass') . trans('common.error'),
                    route('Home::Member::index'));
                break;
            case 'verify_error':
                return $this->error(trans('common.verify_code') . trans('common.error'), route('Home::Member::index'));
                break;
            case 'lock_user_error':
                return $this->error(trans('common.admin') . trans('common.by') . trans('common.lock') . trans('common.please') . config('system.sys_frontend_lock_time') . trans('common.second') . trans('common.again') . trans('common.login'),
                    route('Home::Member::index'));
                break;
            default:
                return $this->success(trans('common.login') . trans('common.success'), route('Home::Member::index'));
        }
    }

    //注册
    public function register()
    {
        if (!$this->verifyCheck(request('verify'), 'register') && config('system.sys_frontend_verify')) {
            return $this->error(trans('common.verify_code') . trans('common.error'),
                route('Home::Member::index', ['t' => 'register']));
        }
        $memberName     = request('re_member_name');
        $memberPwd      = request('password');
        $memberPwdAgain = request('password_again');

        //检测初始化参数是否合法
        $result = $this->doValidateForm('re_member_name', ['re_member_name' => $memberName]);
        if (!$result['status']) {
            return $this->error($result['info'], route('Home::Member::index', ['t' => 'register']));
        }

        $result = $this->doValidateForm('password', ['password' => $memberPwd]);
        if (!$result['status']) {
            return $this->error($result['info'], route('Home::Member::index', ['t' => 'register']));
        }

        $result = $this->doValidateForm('password_again',
            ['password' => $memberPwd, 'password_again' => $memberPwdAgain]);
        if (!$result['status']) {
            return $this->error($result['info'], route('Home::Member::index', ['t' => 'register']));
        }

        //是否自动启用
        $isEnable  = config('system.sys_member_auto_enable') ? 1 : 0;
        $data      = [
            'member_name' => $memberName,
            'member_pwd'  => $memberPwd,
            'group_id'    => [1],
            'is_enable'   => $isEnable,
        ];
        $addResult = Model\Member::create($data);
        if ($addResult) {
            $this->doLogin($memberName, $memberPwd, false);
            return $this->success(trans('common.member') . trans('common.register') . trans('common.success'),
                route('Home::Member::index'));
        } else {
            return $this->error(trans('common.member') . trans('common.register') . trans('common.error'),
                route('Home::Member::index', ['t' => 'register']));
        }
    }

    //登出
    public function logout()
    {
        $this->doLogout();
        return $this->success(trans('common.logout') . trans('common.account') . trans('common.success'),
            route('Home::Index::index'));
    }

    //表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'user':
                $validator = Validator::make($data, [
                    'member_name' => 'user_name',
                ]);
                break;
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
            case 're_member_name':
                $validator = Validator::make($data, [
                    're_member_name' => 'user_name|member_exist',
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

}
