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
// 后台 管理员组

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class AdminGroup extends Backend
{
    //列表
    public function index()
    {
        $AdminGroupModel = D('AdminGroup');
        $where           = array();
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFind_allow = $AdminGroupModel->mFind_allow();
            $where['id']  = array('in', $mFind_allow);
        }
        //建立where
        $v_value                        = '';
        $v_value                        = I('name');
        $v_value && $where['name']      = array('like', '%' . $v_value . '%');
        $v_value                        = I('is_enable');
        $v_value && $where['is_enable'] = (1 == $v_value) ? 1 : 0;

        //初始化翻页 和 列表数据
        $admin_group_list = $AdminGroupModel->mSelect($where, true);
        $this->assign('admin_group_list', $admin_group_list);
        $this->assign('admin_group_list_count', $AdminGroupModel->getPageCount($where));

        //初始化where_info
        $where_info              = array();
        $where_info['name']      = array('type' => 'input', 'name' => L('group') . L('name'));
        $where_info['is_enable'] = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('enable'), 2 => L('disable')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('admin') . L('group') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        if (IS_POST) {
            $data       = $this->_make_data();
            $result_add = $AdminGroupModel->mAdd($data);
            if ($result_add) {
                $this->success(L('management') . L('group') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('management') . L('group') . L('add') . L('error'), U('add'));
            }
        }

        $this->_add_edit_common();
        $this->assign('title', L('admin') . L('group') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $AdminGroupModel->mEdit($id, $data);
            if ($result_edit) {
                $this->success(L('management') . L('group') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('management') . L('group') . L('edit') . L('error'), $error_go_link);
            }
        }
        //获取分组默认信息
        $edit_info = $AdminGroupModel->mFind($id);
        foreach ($edit_info['manage_id'] as $manage_key => $manage_id) {
            $admin_name                          = $AdminModel->mFindColumn($manage_id, 'admin_name');
            $edit_info['manage_id'][$manage_key] = array('value' => $manage_id, 'html' => $admin_name);
        }
        $edit_info['manage_id'] = json_encode($edit_info['manage_id']);
        $this->assign('edit_info', $edit_info);

        $this->_add_edit_common();
        $this->assign('title', L('admin') . L('group') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminGroupModel = D('AdminGroup');
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFind_allow = $AdminGroupModel->mFind_allow();
        }
        if ($id == 1 || (!in_array($id, $mFind_allow) && count(0 < $mFind_allow))) {
            $this->error(L('id') . L('not') . L('del'), U('index'));
        }

        $result_del = $AdminGroupModel->mDel($id);
        if ($result_del) {
            //删除成功后 删除管理员与组的关系
            $AdminModel = D('Admin');
            $AdminModel->mClean($id, 'group_id');
            $this->success(L('management') . L('group') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('management') . L('group') . L('del') . L('error'), U('index'));
        }
    }

    //异步和表单数据验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'name':
                //不能为空
                if ('' == $data['name']) {
                    $result['info'] = L('admin') . L('group') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != C('DEFAULT_CHARSET')) {
                    $data['name'] = iconv(C('DEFAULT_CHARSET'), 'utf-8', $data['name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = L('name_format_error', array('string' => $matches[1]));
                    break;
                }
                //检查管理组名是否存在
                $AdminGroupModel = D('AdminGroup');
                $admin_info      = $AdminGroupModel->mSelect(array('name' => $data['name'], 'id' => array('neq', $data['id'])));
                if (0 < count($admin_info)) {
                    $result['info'] = L('admin') . L('group') . L('name') . L('exists');
                    break;
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
            case 'manage_id':
                isset($data['inserted']) && $where['id']        = array('not in', $data['inserted']);
                $AdminModel                                     = D('Admin');
                isset($data['keyword']) && $where['admin_name'] = array('like', '%' . $data['keyword'] . '%');
                $admin_user_list                                = $AdminModel->mSelect($where);
                foreach ($admin_user_list as $admin_user) {
                    $result['info'][] = array('value' => $admin_user['id'], 'html' => $admin_user['admin_name']);
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function _make_data()
    {
        //初始化参数
        $id        = I('id');
        $manage_id = I('manage_id');
        $add_id    = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manage_id)
            && !in_array($add_id, $manage_id)
        ) {
            $manage_id[] = $add_id;
        }

        $name      = I('name');
        $explains  = I('explains');
        $privilege = I('privilege');
        $is_enable = I('is_enable');

        //检测初始化参数是否合法
        $error_go_link = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $name) {
            $result = $this->_validform('name', array('id' => $id, 'name' => $name));
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

        $data                                                               = array();
        ('add' == ACTION_NAME || null !== $manage_id) && $data['manage_id'] = $manage_id;
        ('add' == ACTION_NAME || null !== $name) && $data['name']           = $name;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains']   = $explains;
        ('add' == ACTION_NAME || null !== $privilege) && $data['privilege'] = $privilege;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable'] = $is_enable;
        return $data;
    }

    //构造管理组assign公共数据
    private function _add_edit_common()
    {
        $this->assign('privilege', $this->_get_privilege('Admin', session('backend_info.privilege')));
    }
}
