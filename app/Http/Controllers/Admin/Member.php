<?php
// 后台 会员

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Member extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('member_name');
        $whereValue && $where['member_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('group_id');
        $whereValue && $where['group_id'] = Model\MemberGroup::where('name', 'like',
            '%' . $whereValue . '%')->first()['id'];
        $whereValue = mMktimeRange('register_time');
        $whereValue && $where[] = ['register_time', $whereValue];
        $whereValue = mMktimeRange('last_time');
        $whereValue && $where[] = ['last_time', $whereValue];

        $memberList = Model\Member::where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($memberList as &$member) {
            foreach ($member['group_id'] as $groupId) {
                $groupName = Model\MemberGroup::colWhere($groupId)->first()['name'];
                isset($member['group_name']) && $member['group_name'] .= " | ";
                $member['group_name'] .= $groupName;
            }
            !isset($member['group_name']) && $member['group_name'] = trans('common.empty');
            !isset($member['add_time']) && $member['add_time'] = trans('common.system') . trans('common.add');
        }
        $assign['member_list'] = $memberList;

        //初始化where_info
        $whereInfo                  = [];
        $whereInfo['member_name']   = ['type' => 'input', 'name' => trans('common.member') . trans('common.name')];
        $whereInfo['group_id']      = ['type' => 'input', 'name' => trans('common.group') . trans('common.name')];
        $whereInfo['register_time'] = ['type' => 'time', 'name' => trans('common.register') . trans('common.time')];
        $whereInfo['last_time']     = ['type' => 'time', 'name' => trans('common.login') . trans('common.time')];
        $assign['where_info']       = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.member') . trans('common.management');
        return view('admin.Member_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Member::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.member') . trans('common.add') . trans('common.success'),
                    route('Admin::Member::index'));
            } else {
                return $this->error(trans('common.member') . trans('common.add') . trans('common.error'),
                    route('Admin::Member::add'));
            }
        }
        $this->addEditCommon();
        $assign['edit_info'] = Model\Member::columnEmptyData();
        $assign['title']     = trans('common.member') . trans('common.add');
        return view('admin.Member_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Member::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = false;
            Model\Member::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('common.member') . trans('common.edit') . trans('common.success'),
                    route('Admin::Member::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Member::index') : route('Admin::Member::edit',
                    ['id' => $id]);
                return $this->error(trans('common.member') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo = Model\Member::colWhere($id)->first()->toArray();
        foreach ($editInfo['group_id'] as &$groupId) {
            $memberGroupName = Model\MemberGroup::colWhere($groupId)->first()['name'];
            $groupId         = ['value' => $groupId, 'html' => $memberGroupName];
        }
        $assign['edit_info'] = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.member') . trans('common.edit');
        return view('admin.Member_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Member::index'));
        }

        //不能删除root用户
        if ($id == 1) {
            return $this->error(trans('common.id') . trans('common.not') . trans('common.del'),
                route('Admin::Member::index'));
        }

        $resultDel = Model\Member::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.member') . trans('common.del') . trans('common.success'),
                route('Admin::Member::index'));
        } else {
            return $this->error(trans('common.member') . trans('common.del') . trans('common.error'),
                route('Admin::Member::index'));
        }
    }

    //配置
    public function setting()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'sys_member_enable',
                'sys_member_auto_enable',
                'sys_frontend_verify',
                'sys_frontend_timeout',
                'sys_frontend_login_num',
                'sys_frontend_lock_time',
            ];
            return $this->_put_config($col, 'system');
        }

        $assign['title'] = trans('common.member') . trans('common.config');
        return view('admin.Member_setting', $assign);
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'member_name':
                //不能为空
                if ('' == $data['member_name']) {
                    $result['info'] = trans('common.member') . trans('common.name') . trans('common.not') . trans('common.empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['member_name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['member_name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['member_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('common.name_format_error', ['string' => $matches[1]]);
                    break;
                }
                //检查用户名是否存在
                $memberInfo = Model\Member::where([
                    'member_name' => $data['member_name'],
                    'id'          => ['neq', $data['id']],
                ])->first()->toArray();
                if ($memberInfo) {
                    $result['info'] = trans('member') . trans('common.name') . trans('common.exists');
                    break;
                }
                break;
            case 'password':
                if ($data['is_pwd'] || '' != $data['password']) {
                    //不能为空
                    if ('' == $data['password']) {
                        $result['info'] = trans('common.pass') . trans('common.not') . trans('common.empty');
                        break;
                    }
                    //密码长度不能小于6
                    if (6 > strlen($data['password'])) {
                        $result['info'] = trans('common.pass_len_error');
                        break;
                    }
                }
                break;
            case 'password_again':
                if ($data['is_pwd'] || '' != $data['password'] || '' != $data['password_again']) {
                    //检测再一次输入的密码是否一致
                    if ($data['password'] != $data['password_again']) {
                        $result['info'] = trans('common.password_again_error');
                        break;
                    }
                    //不能为空
                    if ('' == $data['password_again']) {
                        $result['info'] = trans('common.pass') . trans('common.not') . trans('common.empty');
                        break;
                    }
                }
                break;
            case 'email':
                //检查邮箱名规则
                preg_match('/^(\w+@[\w+\.]+\w+)$/', $data['email'], $matches);
                if ('' != $data['email'] && $matches[1] != $data['email']) {
                    $result['info'] = trans('common.email') . trans('common.format') . trans('common.error');
                    break;
                }
                break;
            case 'phone':
                //检查手机号规则
                preg_match('/^(1\d{10})$/', $data['phone'], $matches);
                if ('' != $data['phone'] && $matches[1] != $data['phone']) {
                    $result['info'] = trans('common.phone') . trans('common.format') . trans('common.error');
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
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = Model\MemberGroup::where($where)->get();
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $id            = request('id');
        $memberName    = request('member_name');
        $password      = request('password');
        $passwordAgain = request('password_again');
        $email    = request('email');
        $phone    = request('phone');
        $groupId  = request('group_id');
        $isEnable = request('is_enable');

        //检测初始化参数是否合法
        if ($id) {
            if (is_array($id)) {
                $errorGoLink = route('Admin::Member::index');
            } else {
                $errorGoLink = route('Admin::Member::edit', ['id' => $id]);
            }
        } else {
            $errorGoLink = route('Admin::Member::add');
        }

        $data     = [];
        if ('add' == $type || null !== $memberName) {
            $result = $this->doValidateForm('member_name', ['id' => $id, 'member_name' => $memberName]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['member_name'] = $memberName;
        }
        if ('add' == $type || null !== $password) {
            $isPwd  = ('add' == $type) ? true : false;
            $result = $this->doValidateForm('password', ['password' => $password, 'is_pwd' => $isPwd]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }

            $result = $this->doValidateForm('password_again',
                ['password' => $password, 'password_again' => $passwordAgain]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }

            $data['member_pwd'] = $password;
        }
        if ('add' == $type || null !== $email) {
            $result = $this->doValidateForm('email', ['email' => $email]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['email'] = $email;
        }
        if ('add' == $type || null !== $phone) {
            $result = $this->doValidateForm('phone', ['phone' => $phone]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['phone'] = $phone;
        }
        if ('add' == $type || null !== $groupId) {
            $data['group_id'] = $groupId;
        }
        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;
        }
        return $data;
    }

    //构造管理员assign公共数据
    private function addEditCommon()
    {
        $memberGroupList             = Model\MemberGroup::all();
        $assign['member_group_list'] = $memberGroupList;
    }
}
