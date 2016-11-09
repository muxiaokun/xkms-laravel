<?php
// Fontend Member Controller 前台成员基础控制器

namespace App\Http\Controllers;

use App\Model;

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
            if (Carbon::now()->getTimestamp() - config('system.sys_frontend_timeout') < $frontendInfo['login_time']->getTimestamp()) {
                $frontendInfo['login_time'] = Carbon::now()->getTimestamp();
                session('frontend_info', $frontendInfo);
            } else {
                $this->doLogout();
                die($this->error(trans('common.login') . trans('common.timeout'), route('Home::Member::index')));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $memberInfo           = Model\Member::mFind($frontendInfo['id']);
            $memberGroupPrivilege = Model\MemberGroup::mFindPrivilege($memberInfo['group_id']);
            if ($frontendInfo['group_privilege']->toArray() !== $memberGroupPrivilege->toArray()) {
                $this->doLogout();
                die($this->error(trans('common.privilege') . trans('common.change') . trans('common.please') . trans('common.login'),
                    route('Home::Member::index')));
            }

            //登录后 检查权限
            if (!$this->_check_privilege()) {
                die($this->error(trans('common.you') . trans('common.none') . trans('common.privilege')));
            }

            //建立会员中心左侧菜单
            $assign['left_nav'] = $this->_get_left_nav();
        } else {
            //检测不登陆就可以访问的
            $allowRoute = [
                'Home::Member::index',
                'Home::Member::login',
                'Home::Member::ajax_api',
                'Home::Member::register',
            ];
            if (!call_user_func_array('Route::is', $allowRoute)) {
                die($this->error(trans('common.not_login') . trans('common.frontend'), route('Home::Member::index')));

            }
        }
    }

    private function _get_left_nav()
    {
        //if(没有在权限中找到列表 就显示默认的列表)
        $memberGroupPriv = session('frontend_info.group_privilege');
        $leftNav         = [];
        //跳过系统基本操作 增删改 异步接口,
        $installMenu = mGetArr(storage_path('app/install_privilege.php'))['Admin'];
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

    public function _check_privilege($actionName = ACTION_NAME, $controllerName = CONTROLLER_NAME)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有权限 3.ajax_api接口 4.不需要权限就能访问的
        $allowAction['Member']     = ['index', 'logout'];
        $allowAction['Uploadfile'] = ['uploadfile', 'managefile'];
        $frontendInfo              = session('frontend_info');
        $memberGroupPriv           = ($frontendInfo['group_privilege']) ? $frontendInfo['group_privilege'] : [];
        if (
            'ajax_api' != $actionName &&
            !in_array($actionName, $allowAction[$controllerName]) &&
            !in_array('all', $memberGroupPriv) &&
            !in_array($controllerName . '_' . $actionName, $memberGroupPriv)
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
