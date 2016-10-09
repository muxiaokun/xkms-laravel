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
        $where            = [];

        //建立where
        $whereValue = '';
        $whereValue = request('member_name');
        $whereValue && $where['member_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('group_id');
        $whereValue && $where['group_id'] = $MemberGroupModel->mFindId(['like', '%' . $whereValue . '%']);
        $whereValue = mMktimeRange('register_time');
        $whereValue && $where['register_time'] = $whereValue;
        $whereValue = mMktimeRange('last_time');
        $whereValue && $where['last_time'] = $whereValue;

        $memberList = $MemberModel->mSelect($where, true);
        foreach ($memberList as &$member) {
            foreach ($member['group_id'] as $groupId) {
                $groupName = $MemberGroupModel->mFindColumn($groupId, 'name');
                isset($member['group_name']) && $member['group_name'] .= " | ";
                $member['group_name'] .= $groupName;
            }
            !isset($member['group_name']) && $member['group_name'] = trans('empty');
            !isset($member['add_time']) && $member['add_time'] = trans('system') . trans('add');
        }
        $this->assign('member_list', $memberList);
        $this->assign('member_list_count', $MemberModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                  = [];
        $whereInfo['member_name']   = ['type' => 'input', 'name' => trans('member') . trans('name')];
        $whereInfo['group_id']      = ['type' => 'input', 'name' => trans('group') . trans('name')];
        $whereInfo['register_time'] = ['type' => 'time', 'name' => trans('register') . trans('time')];
        $whereInfo['last_time']     = ['type' => 'time', 'name' => trans('login') . trans('time')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('member') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $MemberModel = D('Member');
            $data        = $this->makeData();
            $resultAdd   = $MemberModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('member') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('member') . trans('add') . trans('error'), route('add'));
            }
        }
        $this->addEditCommon();
        $this->assign('title', trans('member') . trans('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $MemberModel = D('Member');
        if (IS_POST) {
            $data       = $this->makeData(false);
            $resultEdit = $MemberModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('member') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('member') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo         = $MemberModel->mFind($id);
        $MemberGroupModel = D('MemberGroup');
        foreach ($editInfo['group_id'] as &$groupId) {
            $memberGroupName = $MemberGroupModel->mFindColumn($groupId, 'name');
            $groupId         = ['value' => $groupId, 'html' => $memberGroupName];
        }
        $editInfo['group_id'] = json_encode($editInfo['group_id']);
        $this->assign('edit_info', $editInfo);

        $this->addEditCommon();
        $this->assign('title', trans('member') . trans('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        //不能删除root用户
        if ($id == 1) {
            $this->error(trans('id') . trans('not') . trans('del'), route('index'));
        }

        $MemberModel = D('Member');
        $resultDel   = $MemberModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('member') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('member') . trans('del') . trans('error'), route('index'));
        }
    }

    //配置
    public function setting()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = [
                'SYS_MEMBER_ENABLE',
                'SYS_MEMBER_AUTO_ENABLE',
                'SYS_FRONTEND_VERIFY',
                'SYS_FRONTEND_TIMEOUT',
                'SYS_FRONTEND_LOGIN_NUM',
                'SYS_FRONTEND_LOCK_TIME',
            ];
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', trans('member') . trans('config'));
        $this->display();
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'member_name':
                //不能为空
                if ('' == $data['member_name']) {
                    $result['info'] = trans('member') . trans('name') . trans('not') . trans('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['member_name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['member_name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['member_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('name_format_error', ['string' => $matches[1]]);
                    break;
                }
                //检查用户名是否存在
                $MemberModel = D('Member');
                $memberInfo  = $MemberModel->mSelect([
                    'member_name' => $data['member_name'],
                    'id'          => ['neq', $data['id']],
                ]);
                if (0 < count($memberInfo)) {
                    $result['info'] = trans('member') . trans('name') . trans('exists');
                    break;
                }
                break;
            case 'password':
                if ($data['is_pwd'] || '' != $data['password']) {
                    //不能为空
                    if ('' == $data['password']) {
                        $result['info'] = trans('pass') . trans('not') . trans('empty');
                        break;
                    }
                    //密码长度不能小于6
                    if (6 > strlen($data['password'])) {
                        $result['info'] = trans('pass_len_error');
                        break;
                    }
                }
                break;
            case 'password_again':
                if ($data['is_pwd'] || '' != $data['password'] || '' != $data['password_again']) {
                    //检测再一次输入的密码是否一致
                    if ($data['password'] != $data['password_again']) {
                        $result['info'] = trans('password_again_error');
                        break;
                    }
                    //不能为空
                    if ('' == $data['password_again']) {
                        $result['info'] = trans('pass') . trans('not') . trans('empty');
                        break;
                    }
                }
                break;
            case 'email':
                //检查邮箱名规则
                preg_match('/^(\w+@[\w+\.]+\w+)$/', $data['email'], $matches);
                if ('' != $data['email'] && $matches[1] != $data['email']) {
                    $result['info'] = trans('email') . trans('format') . trans('error');
                    break;
                }
                break;
            case 'phone':
                //检查手机号规则
                preg_match('/^(1\d{10})$/', $data['phone'], $matches);
                if ('' != $data['phone'] && $matches[1] != $data['phone']) {
                    $result['info'] = trans('phone') . trans('format') . trans('error');
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
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'group_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                $MemberGroupModel = D('MemberGroup');
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData($isPwd = true)
    {
        //初始化参数
        $id            = request('id');
        $memberName    = request('member_name');
        $password      = request('password');
        $passwordAgain = request('password_again');
        $email         = request('email');
        $phone         = request('phone');
        $groupId       = request('group_id');
        $isEnable      = request('is_enable');

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', ['id' => $id]);
        if ('add' == ACTION_NAME || null !== $memberName) {
            $result = $this->doValidateForm('member_name', ['id' => $id, 'member_name' => $memberName]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $result = $this->doValidateForm('password', ['password' => $password, 'is_pwd' => $isPwd]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

            $result = $this->doValidateForm('password_again',
                ['password' => $password, 'password_again' => $passwordAgain]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $email) {
            $result = $this->doValidateForm('email', ['email' => $email]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $phone) {
            $result = $this->doValidateForm('phone', ['phone' => $phone]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data = [];
        ('add' == ACTION_NAME || null !== $memberName) && $data['member_name'] = $memberName;
        ('add' == ACTION_NAME || null !== $password) && $data['member_pwd'] = $password;
        ('add' == ACTION_NAME || null !== $email) && $data['email'] = $email;
        ('add' == ACTION_NAME || null !== $phone) && $data['phone'] = $phone;
        ('add' == ACTION_NAME || null !== $groupId) && $data['group_id'] = $groupId;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;
        return $data;
    }

    //构造管理员assign公共数据
    private function addEditCommon()
    {
        $MemberGroupModel = D('MemberGroup');
        $memberGroupList  = $MemberGroupModel->mSelect();
        $this->assign('member_group_list', $memberGroupList);
    }
}
