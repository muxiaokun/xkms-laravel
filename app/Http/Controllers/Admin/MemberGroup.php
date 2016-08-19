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
// 后台 会员组

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class MemberGroup extends Backend
{
    //列表
    public function index()
    {
        $MemberGroupModel = D('MemberGroup');
        $where            = array();

        //建立where
        $v_value                        = '';
        $v_value                        = I('name');
        $v_value && $where['name']      = array('like', '%' . $v_value . '%');
        $v_value                        = I('is_enable');
        $v_value && $where['is_enable'] = (1 == $v_value) ? 1 : 0;

        //初始化翻页 和 列表数据
        $member_group_list = $MemberGroupModel->m_select($where, true);
        $this->assign('member_group_list', $member_group_list);
        $this->assign('member_group_list_count', $MemberGroupModel->get_page_count($where));

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

        $this->assign('title', L('member') . L('group') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        $MemberModel      = D('Member');
        $MemberGroupModel = D('MemberGroup');
        if (IS_POST) {
            $data       = $this->_make_data();
            $result_add = $MemberGroupModel->m_add($data);
            if ($result_add) {
                $this->success(L('member') . L('group') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('member') . L('group') . L('add') . L('error'), U('add'));
            }
        }

        $this->_add_edit_common();
        $this->assign('title', L('member') . L('group') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MemberModel      = D('Member');
        $MemberGroupModel = D('MemberGroup');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $MemberGroupModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('member') . L('group') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('member') . L('group') . L('edit') . L('error'), $error_go_link);
            }
        }
        //获取分组默认信息
        $edit_info = $MemberGroupModel->m_find($id);
        foreach ($edit_info['manage_id'] as $manage_key => $manage_id) {
            $member_name                         = $MemberModel->m_find_column($manage_id, 'member_name');
            $edit_info['manage_id'][$manage_key] = array('value' => $manage_id, 'html' => $member_name);
        }
        $edit_info['manage_id'] = json_encode($edit_info['manage_id']);
        $this->assign('edit_info', $edit_info);

        $this->_add_edit_common();
        $this->assign('title', L('member') . L('group') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {

        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MemberGroupModel = D('MemberGroup');
        $result_del       = $MemberGroupModel->m_del($id);
        if ($result_del) {
            //删除成功后 删除管理员与组的关系
            $MemberModel = D('Member');
            $MemberModel->m_clean($id, 'group_id', 0);
            $this->success(L('member') . L('group') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('member') . L('group') . L('del') . L('error'), U('index'));
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
                    $result['info'] = L('member') . L('group') . L('name') . L('not') . L('empty');
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
                $MemberGroupModel = D('MemberGroup');
                $member_info      = $MemberGroupModel->m_select(array('name' => $data['name'], 'id' => array('neq', $data['id'])));
                if (0 < count($member_info)) {
                    $result['info'] = L('member') . L('group') . L('name') . L('exists');
                    break;
                }
                break;
            case 'privilege':
                //对比权限
                $privilege       = $this->_get_privilege('Home');
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
                isset($data['keyword']) && $where['member_name'] = array('like', '%' . $data['keyword'] . '%');
                isset($data['inserted']) && $where['id']         = array('not in', $data['inserted']);
                $MemberModel                                     = D('Member');
                $member_user_list                                = $MemberModel->m_select($where);
                foreach ($member_user_list as $member_user) {
                    $result['info'][] = array('value' => $member_user['id'], 'html' => $member_user['member_name']);
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

    //构造assign公共数据
    private function _add_edit_common()
    {
        $this->assign('privilege', $this->_get_privilege('Home'));
    }
}
