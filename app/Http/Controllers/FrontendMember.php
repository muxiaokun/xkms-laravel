<?php
// Fontend Member Controller 前台成员基础控制器

namespace App\Http\Controllers;

use App\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class FrontendMember extends Frontend
{
    public function _initialize()
    {
        parent::_initialize();
        //是否启用了会员类的控制器
        if (!config('system.sys_member_enable')) {
            die($this->error(trans('common.member') . trans('common.none') . trans('common.enable'),
                route('Home::Index::index')));
        }

        if ($this->isLogin()) {
            $frontendInfo = session('frontend_info');
            //自动登出时间
            if (Carbon::now()->getTimestamp() - config('system.sys_frontend_timeout') < $frontendInfo['last_time']->getTimestamp()) {
                $frontendInfo['login_time'] = Carbon::now()->getTimestamp();
                session('frontend_info', $frontendInfo);
            } else {
                $this->doLogout();
                die($this->error(trans('common.login') . trans('common.timeout'), route('Home::Member::index')));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $memberGroupPrivilege = Model\MemberGroup::mFindPrivilege($frontendInfo['group_id'])->toArray();
            if ($frontendInfo['group_privilege'] !== $memberGroupPrivilege) {
                $this->doLogout();
                die($this->error(trans('common.privilege') . trans('common.change') . trans('common.please') . trans('common.login'),
                    route('Home::Member::index')));
            }

            //登录后 检查权限
            if (!$this->_check_privilege(Route::currentRouteName())) {
                die($this->error(trans('common.you') . trans('common.none') . trans('common.privilege')));
            }

            //建立会员中心左侧菜单
            //$assign['left_nav'] = $this->_get_left_nav();
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
                die($this->error(trans('common.not_login') . trans('common.frontend'), route('Home::Member::index')));

            }
        }
    }

    protected function _get_left_nav()
    {
        //if(没有在权限中找到列表 就显示默认的列表)
        $memberGroupPriv = session('frontend_info.group_privilege');
        $leftNav         = [];
        //跳过系统基本操作 增删改 异步接口,
        $installMenu = mGetArr(storage_path('app/install_privilege.php'))['Home'];
        foreach ($installMenu as $groupName => $controllers) {
            foreach ($controllers as $actions) {
                foreach ($actions as $actionName => $actionValue) {
                    if (
                        //跳过系统基本操作
                        preg_match('/.*::(add|edit|del)$/', $actionName) ||
                        //跳过没有权限的功能
                        (
                            !in_array('all', $memberGroupPriv) &&
                            !in_array($actionName, $memberGroupPriv)
                        )
                    ) {
                        continue;
                    }

                    $leftNav[] = [
                        'link' => route($actionName),
                        'name' => $actionValue,
                    ];
                }
            }
        }
        $leftNav[] = [
            'link' => route('Home::Member::logout'),
            'name' => trans('common.logout') . trans('common.member'),
        ];
        return $leftNav;
    }

    public function _check_privilege($routeName)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有管理员管理组全部权限 3.拥有权限 4.ajax_api接口 4.不需要权限就能访问的
        $allowRoute     = [
            'Home::Member::index',
            'Home::Member::login',
            'Home::Member::register',
            'Home::Member::logout',
            'ManageUpload::UploadFile',
            'ManageUpload::ManageFile',
        ];
        $backendInfo    = session('backend_info');
        $adminPriv      = $backendInfo['privilege'] ? $backendInfo['privilege'] : [];
        $adminGroupPriv = ($backendInfo['group_privilege']) ? $backendInfo['group_privilege'] : [];
        if (
            'ajax_api' != $routeName &&
            !in_array($routeName, $allowRoute) &&
            !in_array('all', $adminPriv) &&
            !in_array($routeName, $adminPriv) &&
            !in_array('all', $adminGroupPriv) &&
            !in_array($routeName, $adminGroupPriv)
        ) {
            return false;
        }

        return true;
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allowAjaxApi = ['validform', 'get_data'];
        if (!$this->isLogin() && !in_array(request('type'), $allowAjaxApi)) {
            return;
        }

        return $this->doAjaxApi();
    }

}
