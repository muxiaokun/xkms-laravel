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

        if ($this->_is_login()) {
            $frontend_info = session('frontend_info');
            //自动登出时间
            if (time() - C('SYS_FRONTEND_TIMEOUT') < $frontend_info['login_time']) {
                $frontend_info['login_time'] = time();
                session('frontend_info', $frontend_info);
            } else {
                $this->_logout();
                $this->error(L('login') . L('timeout'), U('Member/Index/index'));
            }

            //检查管理员或者管理员组权限变动 先检查数量 提高效率
            $MemberModel            = D('Member');
            $member_info            = $MemberModel->mFind($frontend_info['id']);
            $MemberGroupModel       = D('MemberGroup');
            $member_group_privilege = $MemberGroupModel->mFind_privilege($member_info['group_id']);
            if ($frontend_info['group_privilege'] !== $member_group_privilege) {
                $this->_logout();
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
            $allow_action['Member'] = array('index', 'login', 'verify_img', 'ajax_api', 'register');
            if (!in_array(ACTION_NAME, $allow_action[CONTROLLER_NAME])) {
                $this->error(L('not_login') . L('frontend'), U('Member/index'));
            }
        }
    }

    private function _get_left_nav()
    {
        //if(没有在权限中找到列表 就显示默认的列表)
        $member_group_priv = session('frontend_info.group_privilege');
        $privilege         = F('privilege');
        $left_nav          = array();
        //跳过系统基本操作 增删改 异步接口,
        $deny_link = array('add', 'del', 'edit', 'ajax_port');
        foreach ($privilege['Home'] as $control_group) {
            foreach ($control_group as $control_name => $action) {
                foreach ($action as $action_name => $action_value) {
                    //跳过系统基本操作
                    if (in_array($action_name, $deny_link)) {
                        continue;
                    }

                    //跳过没有权限的功能
                    if (
                        !in_array('all', $member_group_priv) &&
                        !in_array($control_name . '_' . $action_name, $member_group_priv)
                    ) {
                        continue;
                    }

                    $left_nav[] = array(
                        'link' => U('Home/' . $control_name . '/' . $action_name),
                        'name' => $action_value,
                    );
                }
            }
        }
        $left_nav[] = array(
            'link' => U('Home/Member/logout'),
            'name' => L('logout') . L('member'),
        );
        return $left_nav;
    }

    public function _check_privilege($action_name = ACTION_NAME, $controller_name = CONTROLLER_NAME)
    {
        //登录后 检查是否有权限可以操作 1.不是默认框架控制器 2.拥有权限 3.ajax_api接口 4.不需要权限就能访问的
        $allow_action['Member']     = array('index', 'logout');
        $allow_action['Uploadfile'] = array('uploadfile', 'managefile');
        $frontend_info              = session('frontend_info');
        $member_group_priv          = ($frontend_info['group_privilege']) ? $frontend_info['group_privilege'] : array();
        if (
            'ajax_api' != $action_name &&
            !in_array($action_name, $allow_action[$controller_name]) &&
            !in_array('all', $member_group_priv) &&
            !in_array($controller_name . '_' . $action_name, $member_group_priv)
        ) {
            return false;
        }

        return true;
    }

    // 加强ajax_api接口安全性
    public function ajax_api()
    {
        $allow_ajax_api = array('validform', 'get_data');
        if (!$this->_is_login() && !in_array(I('type'), $allow_ajax_api)) {
            return;
        }

        $this->_ajax_api();
    }

}
