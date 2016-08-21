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
// 后台 管理员

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Admin extends Backend
{
    public function test()
    {
        return 'test';
    }
    //列表
    public function index()
    {
        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        $where           = array();
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFind_allow      = $AdminGroupModel->mFind_allow();
            $where['group_id'] = $mFind_allow;
        }
        //建立where
        $v_value                         = '';
        $v_value                         = I('admin_name');
        $v_value && $where['admin_name'] = array('like', '%' . $v_value . '%');
        $v_value                         = I('group_id');
        $v_value && $where['group_id']   = $AdminGroupModel->where(array('name' => array('like', '%' . $v_value . '%')))->col_arr('id');
        $v_value                         = M_mktime_range('last_time');
        $v_value && $where['last_time']  = $v_value;
        $v_value                         = I('is_enable');
        $v_value && $where['is_enable']  = (1 == $v_value) ? 1 : 0;

        //初始化翻页 和 列表数据
        $admin_list = $AdminModel->mSelect($where, true);
        foreach ($admin_list as &$admin) {
            foreach ($admin['group_id'] as $group_id) {
                $group_name = $AdminGroupModel->mFindColumn($group_id, 'name');
                isset($admin['group_name']) && $admin['group_name'] .= " | ";
                $admin['group_name'] .= $group_name;
            }
            !isset($admin['group_name']) && $admin['group_name'] = L('empty');
            !isset($admin['add_time']) && $admin['add_time']     = L('system') . L('add');
        }
        $this->assign('admin_list', $admin_list);
        $this->assign('admin_list_count', $AdminModel->getPageCount($where));

        //初始化where_info
        $where_info               = array();
        $where_info['admin_name'] = array('type' => 'input', 'name' => L('admin') . L('name'));
        $where_info['group_id']   = array('type' => 'input', 'name' => L('group') . L('name'));
        $where_info['last_time']  = array('type' => 'time', 'name' => L('login') . L('time'));
        $where_info['is_enable']  = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('enable'), 2 => L('disable')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('admin') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $AdminModel = D('Admin');
            $data       = $this->_make_data();
            $result_add = $AdminModel->mAdd($data);
            if ($result_add) {
                $this->success(L('admin') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('admin') . L('add') . L('error'), U('add'));
            }
        }
        $this->_add_edit_common();
        $this->assign('title', L('admin') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminModel = D('Admin');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $AdminModel->mEdit($id, $data);
            if ($result_edit) {
                $this->success(L('admin') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('admin') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info       = $AdminModel->mFind($id);
        $AdminGroupModel = D('AdminGroup');
        foreach ($edit_info['group_id'] as &$group_id) {
            $admin_group_name = $AdminGroupModel->mFindColumn($group_id, 'name');
            $group_id         = array('value' => $group_id, 'html' => $admin_group_name);
        }
        $edit_info['group_id'] = json_encode($edit_info['group_id']);
        $this->assign('edit_info', $edit_info);

        $this->_add_edit_common();
        $this->assign('title', L('admin') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminModel = D('Admin');
        $result_del = $AdminModel->mDel($id);
        if ($result_del) {
            $this->success(L('admin') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('admin') . L('del') . L('error'), U('index'));
        }
    }

    //配置
    public function setting()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'SYS_ADMIN_AUTO_LOG',
                'SYS_BACKEND_VERIFY',
                'SYS_BACKEND_TIMEOUT',
                'SYS_BACKEND_LOGIN_NUM',
                'SYS_BACKEND_LOCK_TIME',
            );
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', L('admin') . L('config'));
        $this->display();
    }

    //异步和表单数据验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'admin_name':
                //不能为空
                if ('' == $data['admin_name']) {
                    $result['info'] = L('admin') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != C('DEFAULT_CHARSET')) {
                    $data['admin_name'] = iconv(C('DEFAULT_CHARSET'), 'utf-8', $data['admin_name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['admin_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = L('name_format_error', array('string' => $matches[1]));
                    break;
                }
                //检查用户名是否存在
                $AdminModel = D('Admin');
                $admin_info = $AdminModel->mSelect(array('admin_name' => $data['admin_name'], 'id' => array('neq', $data['id'])));
                if (0 < count($admin_info)) {
                    $result['info'] = L('admin') . L('name') . L('exists');
                    break;
                }
                break;
            case 'password':
                if ($data['is_pwd'] || '' != $data['password']) {
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
                }
                break;
            case 'password_again':
                if ($data['is_pwd'] || '' != $data['password'] || '' != $data['password_again']) {
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
                }
                break;
            case 'privilege':
                //对比权限
                $privilege       = $this->_get_privilege('Admin', session('backend_info.privilege'));
                $check_privilege = array();
                foreach ($privilege as $controller_cn => $privs) {
                    foreach ($privs as $controller_name => $controller) {
                        foreach ($controller as $action_name => $action) {
                            $check_privilege[] = $controller_name . '_' . $action_name;
                        }
                    }
                }
                foreach ($data as $priv) {
                    if (!in_array($priv, $check_privilege)) {
                        $result['info'] = L('privilege') . L('submit') . L('error');
                        break;
                    }
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //异步数据获取
    protected function _get_data($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'group_id':
                if (1 != session('backend_info.id')) {
                    $where['manage_id'] = session('backend_info.id');
                }
                isset($data['inserted']) && $where['id']  = array('not in', $data['inserted']);
                $AdminGroupModel                          = D('AdminGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $admin_group_list                         = $AdminGroupModel->mSelect($where);
                foreach ($admin_group_list as $admin_group) {
                    $result['info'][] = array('value' => $admin_group['id'], 'html' => $admin_group['name']);
                }
                break;
        }
        return $result;
    }

    //构造数据
    //$is_pwd 是否检测密码规则
    private function _make_data()
    {
        //初始化参数
        $id             = I('id');
        $admin_name     = I('admin_name');
        $password       = I('password');
        $password_again = I('password_again');
        $group_id       = I('group_id');
        $privilege      = I('privilege');
        $is_enable      = I('is_enable');

        $error_go_link = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        //检测初始化参数是否合法
        if ('add' == ACTION_NAME || null !== $admin_name) {
            $result = $this->_validform('admin_name', array('id' => $id, 'admin_name' => $admin_name));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $is_pwd = ('add' == ACTION_NAME) ? true : false;
            $result = $this->_validform('password', array('password' => $password, 'is_pwd' => $is_pwd));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $result = $this->_validform('password_again', array('password' => $password, 'password_again' => $password_again));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }
        if ('add' == ACTION_NAME || null !== $privilege) {
            $result = $this->_validform('privilege', $privilege);
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }

        //最高级管理不检查该项 管理员可否被当前管理员添加编辑
        if (1 != session('backend_info.id')) {
            $AdminGroupModel = D('AdminGroup');
            $mFind_allow    = $AdminGroupModel->mFind_allow();
            if (!M_in_array($group_id, $mFind_allow)) {
                $this->error(L('you') . L('none') . L('privilege'));
            }

        }

        $data                                                                 = array();
        ('add' == ACTION_NAME || null !== $admin_name) && $data['admin_name'] = $admin_name;
        ('add' == ACTION_NAME || null !== $password) && $data['admin_pwd']    = $password;
        ('add' == ACTION_NAME || null !== $group_id) && $data['group_id']     = $group_id;
        ('add' == ACTION_NAME || null !== $privilege) && $data['privilege']   = $privilege;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable']   = $is_enable;
        return $data;
    }

    //构造管理员assign公共数据
    private function _add_edit_common()
    {
        $this->assign('privilege', $this->_get_privilege('Admin', session('backend_info.privilege')));
    }
}
