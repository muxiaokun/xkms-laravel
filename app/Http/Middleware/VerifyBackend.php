<?php

namespace App\Http\Middleware;

use Closure;
use App\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend;

class VerifyBackend extends Common
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $backend = new Backend();
        if ($backend->isLogin()) {
            $backendInfo = session('backend_info');
            //自动登出时间
            if (Carbon::now()->getTimestamp() - config('system.sys_backend_timeout') < $backendInfo['last_time']->getTimestamp()) {
                session(['backend_info.last_time' => Carbon::now()]);
            } else {
                $backend->doLogout();
                return $this->error(trans('common.login') . trans('common.timeout'), route('Admin::Index::index'));
            }
            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $adminInfo           = Model\Admin::colWhere($backendInfo['id'])->first();
            $adminGroupPrivilege = Model\AdminGroup::mFindPrivilege($adminInfo['group_id'])->toArray();
            if (
                $backendInfo['privilege'] !== $adminInfo['privilege'] ||
                $backendInfo['group_privilege'] !== $adminGroupPrivilege
            ) {
                $backend->doLogout();
                return $this->error(trans('common.privilege') . trans('common.change') . trans('common.please') . trans('common.login'),
                    route('Admin::Index::index'));
            }

            //登录后 检查权限
            if (!$backend->_check_privilege(Route::currentRouteName())) {
                return $this->error(trans('common.you') . trans('common.none') . trans('common.privilege'));
            }

            //是否开启管理员日志 记录POST提交数据除了root用户
            if (config('system.sys_admin_auto_log') && 1 != session('backend_info.id') && request()->isMethod('POST')) {
                Model\AdminLog::record($backendInfo['id']);
            }
        } else {
            //检测不登陆就可以访问的
            $allowRoute = [
                'Admin::Index::index',
                'Admin::Index::login',
                'Admin::Index::logout',
            ];
            if (!call_user_func_array('Route::is', $allowRoute)) {
                return $this->error(trans('common.not_login') . trans('common.backend'), route('Admin::Index::index'));

            }
        }

        return $response;
    }
}
