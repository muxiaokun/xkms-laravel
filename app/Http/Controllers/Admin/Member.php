<?php
// 后台 会员

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\Validator;

class Member extends Backend
{
    //列表
    public function index()
    {

        $memberList              = Model\Member::where(function ($query) {
            $member_name = request('member_name');
            if ($member_name) {
                $query->where('member_name', 'like', '%' . $member_name . '%');
            }

            $group_id = request('group_id');
            if ($group_id) {
                $search_ids = Model\AdminGroup::where('name', 'like', '%' . $group_id . '%')->pluck('id');
                //搜索结果为空时 添加错误条件
                if ($search_ids->isEmpty()) {
                    $search_ids->push(-1);
                }
                $query->transfixionWhere('group_id', $search_ids);
            }

            $created_at = mMktimeRange('created_at');
            if ($created_at) {
                $query->timeWhere('created_at', $created_at);
            }

            $last_time = mMktimeRange('last_time');
            if ($last_time) {
                $query->timeWhere('last_time', $last_time);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($memberList as &$member) {
            if ($member['group_id']->isEmpty()) {
                $member['group_name'] = trans('common.empty');
            } else {
                Model\MemberGroup::colWhere($member['group_id']->toArray())->get()->pluck('name');
            }
        }
        $assign['member_list'] = $memberList;

        //初始化where_info
        $whereInfo                  = [];
        $whereInfo['member_name']   = ['type' => 'input', 'name' => trans('common.member') . trans('common.name')];
        $whereInfo['group_id']      = ['type' => 'input', 'name' => trans('common.group') . trans('common.name')];
        $whereInfo['created_at'] = ['type' => 'time', 'name' => trans('common.register') . trans('common.time')];
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
                $validator = Validator::make($data, [
                    'member_name' => 'user_name|member_exist',
                ]);
                break;
            case 'password':
                $validator = Validator::make($data, [
                    'password' => 'password:' . $data['is_pwd'],
                ]);
                break;
            case 'password_confirmation':
                $validator = Validator::make($data, [
                    'password' => 'confirmed',
                ]);
                break;
            case 'email':
                $validator = Validator::make($data, [
                    'email' => 'email',
                ]);
                break;
            case 'phone':
                $validator = Validator::make($data, [
                    'phone' => 'phone',
                ]);
                break;
        }

        if (isset($validator) && $validator->fails()) {
            $result['info'] = implode('', $validator->errors()->all());
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'group_id':
                Model\MemberGroup::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['name']];
                });
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
        if (('add' == $type || null !== $password) && '' === $password) {
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
