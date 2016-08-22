<?php
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
        $whereValue                            = '';
        $whereValue                            = I('member_name');
        $whereValue && $where['member_name']   = array('like', '%' . $whereValue . '%');
        $whereValue                            = I('group_id');
        $whereValue && $where['group_id']      = $MemberGroupModel->mFindId(array('like', '%' . $whereValue . '%'));
        $whereValue                            = mMktimeRange('register_time');
        $whereValue && $where['register_time'] = $whereValue;
        $whereValue                            = mMktimeRange('last_time');
        $whereValue && $where['last_time']     = $whereValue;

        $memberList = $MemberModel->mSelect($where, true);
        foreach ($memberList as &$member) {
            foreach ($member['group_id'] as $groupId) {
                $groupName = $MemberGroupModel->mFindColumn($groupId, 'name');
                isset($member['group_name']) && $member['group_name'] .= " | ";
                $member['group_name'] .= $groupName;
            }
            !isset($member['group_name']) && $member['group_name'] = L('empty');
            !isset($member['add_time']) && $member['add_time']     = L('system') . L('add');
        }
        $this->assign('member_list', $memberList);
        $this->assign('member_list_count', $MemberModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                  = array();
        $whereInfo['member_name']   = array('type' => 'input', 'name' => L('member') . L('name'));
        $whereInfo['group_id']      = array('type' => 'input', 'name' => L('group') . L('name'));
        $whereInfo['register_time'] = array('type' => 'time', 'name' => L('register') . L('time'));
        $whereInfo['last_time']     = array('type' => 'time', 'name' => L('login') . L('time'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('member') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $MemberModel = D('Member');
            $data        = $this->makeData();
            $resultAdd  = $MemberModel->mAdd($data);
            if ($resultAdd) {
                $this->success(L('member') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('member') . L('add') . L('error'), U('add'));
            }
        }
        $this->addEditCommon();
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
            $data        = $this->makeData(false);
            $resultEdit = $MemberModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(L('member') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('member') . L('edit') . L('error'), $errorGoLink);
            }
        }

        $editInfo        = $MemberModel->mFind($id);
        $MemberGroupModel = D('MemberGroup');
        foreach ($editInfo['group_id'] as &$groupId) {
            $memberGroupName = $MemberGroupModel->mFindColumn($groupId, 'name');
            $groupId          = array('value' => $groupId, 'html' => $memberGroupName);
        }
        $editInfo['group_id'] = json_encode($editInfo['group_id']);
        $this->assign('edit_info', $editInfo);

        $this->addEditCommon();
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
        $resultDel  = $MemberModel->mDel($id);
        if ($resultDel) {
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
    protected function doValidateForm($field, $data)
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
                $memberInfo = $MemberModel->mSelect(array('member_name' => $data['member_name'], 'id' => array('neq', $data['id'])));
                if (0 < count($memberInfo)) {
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
    protected function getData($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'group_id':
                isset($data['inserted']) && $where['id']  = array('not in', $data['inserted']);
                $MemberGroupModel                         = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $memberGroupList                        = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = array('value' => $memberGroup['id'], 'html' => $memberGroup['name']);
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData($isPwd = true)
    {
        //初始化参数
        $id             = I('id');
        $memberName    = I('member_name');
        $password       = I('password');
        $passwordAgain = I('password_again');
        $email          = I('email');
        $phone          = I('phone');
        $groupId       = I('group_id');
        $isEnable      = I('is_enable');

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $memberName) {
            $result = $this->doValidateForm('member_name', array('id' => $id, 'member_name' => $memberName));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $result = $this->doValidateForm('password', array('password' => $password, 'is_pwd' => $isPwd));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

            $result = $this->doValidateForm('password_again', array('password' => $password, 'password_again' => $passwordAgain));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $email) {
            $result = $this->doValidateForm('email', array('email' => $email));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $phone) {
            $result = $this->doValidateForm('phone', array('phone' => $phone));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data                                                                   = array();
        ('add' == ACTION_NAME || null !== $memberName) && $data['member_name'] = $memberName;
        ('add' == ACTION_NAME || null !== $password) && $data['member_pwd']     = $password;
        ('add' == ACTION_NAME || null !== $email) && $data['email']             = $email;
        ('add' == ACTION_NAME || null !== $phone) && $data['phone']             = $phone;
        ('add' == ACTION_NAME || null !== $groupId) && $data['group_id']       = $groupId;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable']     = $isEnable;
        return $data;
    }

    //构造管理员assign公共数据
    private function addEditCommon()
    {
        $MemberGroupModel  = D('MemberGroup');
        $memberGroupList = $MemberGroupModel->mSelect();
        $this->assign('member_group_list', $memberGroupList);
    }
}
