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
// Fontend Member Controller 前台成员基础控制器

namespace App\Http\Controllers;

class FrontendMember extends Frontend
{
    public function _initialize()
    {
        parent::_initialize();
        //是否启用了会员类的控制器
        if (!C('SYS_MEMBER_ENABLE')) {
            $this->error(L('member') . L('none') . L('enable'), U('Index/index'));
        }

        if ($this->isLogin()) {
            $frontendInfo = session('frontend_info');
            //自动登出时间
            if (time() - C('SYS_FRONTEND_TIMEOUT') < $frontendInfo['login_time']) {
                $frontendInfo['login_time'] = time();
                session('frontend_info', $frontendInfo);
            } else {
                $this->doLogout();
                $this->error(L('login') . L('timeout'), U('Member/Index/index'));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $MemberModel            = D('Member');
            $memberInfo            = $MemberModel->mFind($frontendInfo['id']);
            $MemberGroupModel       = D('MemberGroup');
            $memberGroupPrivilege = $MemberGroupModel->mFind_privilege($memberInfo['group_id']);
            if ($frontendInfo['group_privilege'] !== $memberGroupPrivilege) {
                $this->doLogout();
                $this->error(L('privilege') . L('change') . L('please') . L('login'), U('Member/index'));
            }

            //登录后 检查权限
            if (!$this->_check_privilege()) {
                $this->error(L('you') . L('none') . L('privilege'));
            }

            //建立会员中心左侧菜单
            $this->assign('left_nav', $this->_get_left_nav());
        } else {
            //检测不登陆就可以访问的
            $allowAction['Member'] = array('index', 'login', 'verifyImg', 'ajax_api', 'register');
            if (!in_array(ACTION_NAME, $allowAction[CONTROLLER_NAME])) {
                $this->error(L('notdoLogin') . L('frontend'), U('Member/index'));
            }
        }
    }

    private function _get_left_nav()
    {
        //if(没有在权限中找到列表 就显示默认的列表)
        $memberGroupPriv = session('frontend_info.group_privilege');
        $privilege         = F('privilege');
        $leftNav          = array();
        //跳过系统基本操作 增删改 异步接口,
        $denyLink = array('add', 'del', 'edit', 'ajax_port');
        foreach ($privilege['Home'] as $controlGroup) {
            foreach ($controlGroup as $controlName => $action) {
                foreach ($action as $actionName => $actionValue) {
                    //跳过系统基本操作
                    if (in_array($actionName, $denyLink)) {
                        continue;
                    }

                    //跳过没有权限的功能
                    if (
                        !in_array('all', $memberGroupPriv) &&
                        !in_array($controlName . '_' . $actionName, $memberGroupPriv)
                    ) {
                        continue;
                    }

                    $leftNav[] = array(
                        'link' => U('Home/' . $controlName . '/' . $actionName),
                        'name' => $actionValue,
                    );
                }
            }
        }
        $leftNav[] = array(
            'link' => U('Home/Member/logout'),
            'name' => L('logout') . L('member'),
        );
        return $leftNav;
    }

    public function _check_privilege($actionName = ACTION_NAME, $controllerName = CONTROLLER_NAME)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有权限 3.ajax_api接口 4.不需要权限就能访问的
        $allowAction['Member']     = array('index', 'logout');
        $allowAction['Uploadfile'] = array('uploadfile', 'managefile');
        $frontendInfo              = session('frontend_info');
        $memberGroupPriv          = ($frontendInfo['group_privilege']) ? $frontendInfo['group_privilege'] : array();
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
        $allowAjaxApi = array('validform', 'get_data');
        if (!$this->isLogin() && !in_array(I('type'), $allowAjaxApi)) {
            return;
        }

        $this->_ajax_api();
    }

}
