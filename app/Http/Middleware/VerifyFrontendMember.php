<?php

namespace App\Http\Middleware;

use Closure;
use App\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendMember;

class VerifyFrontendMember extends Common
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $frontendMember = new FrontendMember();
        //是否启用了会员类的控制器
        if (!config('system.sys_member_enable')) {
            return $this->error(trans('common.member') . trans('common.none') . trans('common.enable'),
                route('Home::Index::index'));
        }

        if ($frontendMember->isLogin()) {
            $frontendInfo = session('frontend_info');
            //自动登出时间
            if (Carbon::now()->getTimestamp() - config('system.sys_frontend_timeout') < $frontendInfo['last_time']->getTimestamp()) {
                session(['frontend_info.last_time' => Carbon::now()]);
            } else {
                $frontendMember->doLogout();
                return $this->error(trans('common.login') . trans('common.timeout'), route('Home::Member::index'));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $memberGroupPrivilege = Model\MemberGroup::mFindPrivilege($frontendInfo['group_id'])->toArray();
            if ($frontendInfo['group_privilege'] !== $memberGroupPrivilege) {
                $frontendMember->doLogout();
                return $this->error(trans('common.privilege') . trans('common.change') . trans('common.please') . trans('common.login'),
                    route('Home::Member::index'));
            }

            //登录后 检查权限
            if (!$frontendMember->_check_privilege(Route::currentRouteName())) {
                return $this->error(trans('common.you') . trans('common.none') . trans('common.privilege'));
            }

        } else {
            //检测不登陆就可以访问的
            $allowRoute = [
                'Home::Member::index',
                'Home::Member::login',
                'Home::Member::register',
                'Home::Member::logout',
                'Home::Member::ajax_api',
            ];
            if (!call_user_func_array('Route::is', $allowRoute)) {
                return $this->error(trans('common.not_login') . trans('common.frontend'), route('Home::Member::index'));

            }
        }

        return $response;
    }
}
