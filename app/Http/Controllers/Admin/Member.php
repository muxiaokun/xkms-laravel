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
// 后台 会员

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Member extends Backend
{
    //列表
    public function index()
    {
        $MemberModel      = D('Member');
        $MemberGroupModel = D('MemberGroup');
        $where            = array();

        //建立where
        $v_value                            = '';
        $v_value                            = I('member_name');
        $v_value && $where['member_name']   = array('like', '%' . $v_value . '%');
        $v_value                            = I('group_id');
        $v_value && $where['group_id']      = $MemberGroupModel->m_find_id(array('like', '%' . $v_value . '%'));
        $v_value                            = M_mktime_range('register_time');
        $v_value && $where['register_time'] = $v_value;
        $v_value                            = M_mktime_range('last_time');
        $v_value && $where['last_time']     = $v_value;

        $member_list = $MemberModel->m_select($where, true);
        foreach ($member_list as &$member) {
            foreach ($member['group_id'] as $group_id) {
                $group_name = $MemberGroupModel->m_find_column($group_id, 'name');
                isset($member['group_name']) && $member['group_name'] .= " | ";
                $member['group_name'] .= $group_name;
            }
            !isset($member['group_name']) && $member['group_name'] = L('empty');
            !isset($member['add_time']) && $member['add_time']     = L('system') . L('add');
        }
        $this->assign('member_list', $member_list);
        $this->assign('member_list_count', $MemberModel->get_page_count($where));

        //初始化where_info
        $where_info                  = array();
        $where_info['member_name']   = array('type' => 'input', 'name' => L('member') . L('name'));
        $where_info['group_id']      = array('type' => 'input', 'name' => L('group') . L('name'));
        $where_info['register_time'] = array('type' => 'time', 'name' => L('register') . L('time'));
        $where_info['last_time']     = array('type' => 'time', 'name' => L('login') . L('time'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('member') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $MemberModel = D('Member');
            $data        = $this->_make_data();
            $result_add  = $MemberModel->m_add($data);
            if ($result_add) {
                $this->success(L('member') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('member') . L('add') . L('error'), U('add'));
            }
        }
        $this->_add_edit_common();
        $this->assign('title', L('member') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MemberModel = D('Member');
        if (IS_POST) {
            $data        = $this->_make_data(false);
            $result_edit = $MemberModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('member') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('member') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info        = $MemberModel->m_find($id);
        $MemberGroupModel = D('MemberGroup');
        foreach ($edit_info['group_id'] as &$group_id) {
            $member_group_name = $MemberGroupModel->m_find_column($group_id, 'name');
            $group_id          = array('value' => $group_id, 'html' => $member_group_name);
        }
        $edit_info['group_id'] = json_encode($edit_info['group_id']);
        $this->assign('edit_info', $edit_info);

        $this->_add_edit_common();
        $this->assign('title', L('member') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        //不能删除root用户
        if ($id == 1) {
            $this->error(L('id') . L('not') . L('del'), U('index'));
        }

        $MemberModel = D('Member');
        $result_del  = $MemberModel->m_del($id);
        if ($result_del) {
            $this->success(L('member') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('member') . L('del') . L('error'), U('index'));
        }
    }

    //配置
    public function setting()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'SYS_MEMBER_ENABLE',
                'SYS_MEMBER_AUTO_ENABLE',
                'SYS_FRONTEND_VERIFY',
                'SYS_FRONTEND_TIMEOUT',
                'SYS_FRONTEND_LOGIN_NUM',
                'SYS_FRONTEND_LOCK_TIME',
            );
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', L('member') . L('config'));
        $this->display();
    }

    //异步和表单数据验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'member_name':
                //不能为空
                if ('' == $data['member_name']) {
                    $result['info'] = L('member') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != C('DEFAULT_CHARSET')) {
                    $data['member_name'] = iconv(C('DEFAULT_CHARSET'), 'utf-8', $data['member_name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['member_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = L('name_format_error', array('string' => $matches[1]));
                    break;
                }
                //检查用户名是否存在
                $MemberModel = D('Member');
                $member_info = $MemberModel->m_select(array('member_name' => $data['member_name'], 'id' => array('neq', $data['id'])));
                if (0 < count($member_info)) {
                    $result['info'] = L('member') . L('name') . L('exists');
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
            case 'email':
                //检查邮箱名规则
                preg_match('/^(\w+@[\w+\.]+\w+)$/', $data['email'], $matches);
                if ('' != $data['email'] && $matches[1] != $data['email']) {
                    $result['info'] = L('email') . L('format') . L('error');
                    break;
                }
                break;
            case 'phone':
                //检查手机号规则
                preg_match('/^(1\d{10})$/', $data['phone'], $matches);
                if ('' != $data['phone'] && $matches[1] != $data['phone']) {
                    $result['info'] = L('phone') . L('format') . L('error');
                    break;
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
                isset($data['inserted']) && $where['id']  = array('not in', $data['inserted']);
                $MemberGroupModel                         = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $member_group_list                        = $MemberGroupModel->m_select($where);
                foreach ($member_group_list as $member_group) {
                    $result['info'][] = array('value' => $member_group['id'], 'html' => $member_group['name']);
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function _make_data($is_pwd = true)
    {
        //初始化参数
        $id             = I('id');
        $member_name    = I('member_name');
        $password       = I('password');
        $password_again = I('password_again');
        $email          = I('email');
        $phone          = I('phone');
        $group_id       = I('group_id');
        $is_enable      = I('is_enable');

        //检测初始化参数是否合法
        $error_go_link = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $member_name) {
            $result = $this->_validform('member_name', array('id' => $id, 'member_name' => $member_name));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $result = $this->_validform('password', array('password' => $password, 'is_pwd' => $is_pwd));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

            $result = $this->_validform('password_again', array('password' => $password, 'password_again' => $password_again));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }
        if ('add' == ACTION_NAME || null !== $email) {
            $result = $this->_validform('email', array('email' => $email));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }
        if ('add' == ACTION_NAME || null !== $phone) {
            $result = $this->_validform('phone', array('phone' => $phone));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }

        $data                                                                   = array();
        ('add' == ACTION_NAME || null !== $member_name) && $data['member_name'] = $member_name;
        ('add' == ACTION_NAME || null !== $password) && $data['member_pwd']     = $password;
        ('add' == ACTION_NAME || null !== $email) && $data['email']             = $email;
        ('add' == ACTION_NAME || null !== $phone) && $data['phone']             = $phone;
        ('add' == ACTION_NAME || null !== $group_id) && $data['group_id']       = $group_id;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable']     = $is_enable;
        return $data;
    }

    //构造管理员assign公共数据
    private function _add_edit_common()
    {
        $MemberGroupModel  = D('MemberGroup');
        $member_group_list = $MemberGroupModel->m_select();
        $this->assign('member_group_list', $member_group_list);
    }
}
