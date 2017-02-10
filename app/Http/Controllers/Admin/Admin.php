<?php
// 后台 管理员

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class Admin extends Backend
{
    //列表
    public function index()
    {

        //初始化翻页 和 列表数据
        $adminList = Model\Admins::where(function ($query) {
            $login_id = session('backend_info.id');
            $ids      = [];
            if (1 != $login_id) {
                //非root需要权限
                $ids = Model\AdminGroups::where('manage_id', 'like', '%|' . $login_id . '|%')->pluck('id');
                $query->transfixionWhere('group_id', $ids);
            }

            $admin_name = request('admin_name');
            if ($admin_name) {
                $query->where('admin_name', 'like', '%' . $admin_name . '%');
            }

            $group_id = request('group_id');
            if ($group_id) {
                $search_ids = Model\AdminGroups::where('name', 'like', '%' . $group_id . '%')->pluck('id');
                //管理组权限检测
                if ($ids) {
                    $search_ids = $search_ids->intersect($ids);
                }
                //搜索结果为空时 添加错误条件
                if ($search_ids->isEmpty()) {
                    $search_ids->push(-1);
                }
                $query->transfixionWhere('group_id', $search_ids);
            }

            $last_time = mMktimeRange('last_time');
            if ($last_time) {
                $query->timeWhere('last_time', $last_time);
            }

            $is_enable = request('is_enable');
            if ($is_enable) {
                $query->where('is_enable', '=', (1 == $is_enable) ? 1 : 0);
            }

        })->paginate(config('system.sys_max_row'));

        foreach ($adminList as &$admin) {
            foreach ($admin['group_id'] as $groupId) {
                $groupName = Model\AdminGroups::colWhere($groupId)->first()['name'];
                isset($admin['group_name']) && $admin['group_name'] .= " | ";
                $admin['group_name'] .= $groupName;
            }
            !isset($admin['group_name']) && $admin['group_name'] = trans('common.empty');
            !isset($admin['add_time']) && $admin['add_time'] = trans('common.system') . trans('common.add');
        }
        $assign['admin_list'] = $adminList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['admin_name'] = ['type' => 'input', 'name' => trans('common.admin') . trans('common.name')];
        $whereInfo['group_id']   = ['type' => 'input', 'name' => trans('common.group') . trans('common.name')];
        $whereInfo['last_time']  = ['type' => 'time', 'name' => trans('common.login') . trans('common.time')];
        $whereInfo['is_enable']  = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
            'value' => [1 => trans('common.enable'), 2 => trans('common.disable')],
        ];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.admin') . trans('common.management');
        return view('admin.Admin_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }
            $resultAdd = Model\Admins::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.admin') . trans('common.add') . trans('common.success'),
                    route('Admin::Admin::index'));
            } else {
                return $this->error(trans('common.admin') . trans('common.add') . trans('common.error'));
            }
        }
        $this->addEditCommon();
        $assign['edit_info'] = Model\Admins::columnEmptyData();
        $assign['title']     = trans('common.admin') . trans('common.add');
        return view('admin.Admin_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Admin::index'));
        }

        if (request()->isMethod('POST')) {
            $data = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }
            $resultEdit = Model\Admins::colWhere($id)->first()->update($data);
            if ($resultEdit) {
                return $this->success(trans('common.admin') . trans('common.edit') . trans('common.success'),
                    route('Admin::Admin::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Admin::index') : route('Admin::Admin::edit',
                    ['id' => $id]);
                return $this->error(trans('common.admin') . trans('common.edit') . trans('common.error'), $errorGoLink);
            }
        }

        $editInfo = Model\Admins::colWhere($id)->first()->toArray();
        foreach ($editInfo['group_id'] as &$groupId) {
            $adminGroupName = Model\AdminGroups::colWhere($groupId)->first()['name'];
            $groupId        = ['value' => $groupId, 'html' => $adminGroupName];
        }
        $assign['edit_info'] = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.admin') . trans('common.edit');
        return view('admin.Admin_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Admin::index'));
        }

        $resultDel = false;
        if (1 != $id || (is_array($id) && !in_array(1, $id))) {
            $resultDel = Model\Admins::destroy($id);
        }
        if ($resultDel) {
            return $this->success(trans('common.admin') . trans('common.del') . trans('common.success'),
                route('Admin::Admin::index'));

        } else {
            return $this->error(trans('common.admin') . trans('common.del') . trans('common.error'),
                route('Admin::Admin::index'));
        }
    }

    //配置
    public function setting()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'sys_admin_auto_log',
                'sys_backend_verify',
                'sys_backend_timeout',
                'sys_backend_login_num',
                'sys_backend_lock_time',
            ];
            return $this->_put_config($col, 'system');
        }

        $assign['title'] = trans('common.admin') . trans('common.config');
        return view('admin.Admin_setting', $assign);
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'admin_name':
                $validator = Validator::make($data, [
                    'admin_name' => 'admin_exist|user_name',
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
            case 'privilege':
                $validator = Validator::make($data, [
                    'privilege' => 'privilege:backend_info',
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
                $adminGroupList = Model\AdminGroups::where(function ($query) use ($data) {
                    $login_id = session('backend_info.id');
                    $ids      = [];
                    if (1 != $login_id) {
                        //非root需要权限
                        $ids = Model\AdminGroups::where('manage_id', 'like',
                            '%|' . $login_id . '|%')->pluck('id')->push(-1);
                        $query->transfixionWhere('group_id', $ids);
                    }

                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get();
                foreach ($adminGroupList as $adminGroup) {
                    $result['info'][] = ['value' => $adminGroup['id'], 'html' => $adminGroup['name']];
                }
                break;
        }

        return $result;
    }

    //构造数据
    //$isPwd 是否检测密码规则
    private function makeData($type)
    {
        //初始化参数
        $id                   = request('id');
        $adminName            = request('admin_name');
        $password             = request('password');
        $passwordConfirmation = request('password_confirmation');
        $groupId              = request('group_id');
        $privilege            = request('privilege');
        $isEnable             = request('is_enable');

        if ($id) {
            if (is_array($id)) {
                $errorGoLink = route('Admin::Admin::index');
            } else {
                $errorGoLink = route('Admin::Admin::edit', ['id' => $id]);
            }
        } else {
            $errorGoLink = route('Admin::Admin::add');
        }

        $data                 = [];
        //检测初始化参数是否合法
        if ('add' == $type || null !== $adminName) {
            $result = $this->doValidateForm('admin_name', ['id' => $id, 'admin_name' => $adminName]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['admin_name'] = $adminName;
        }
        if ('add' == $type || null !== $password) {
            $isPwd  = ('add' == $type) ? true : false;
            $result = $this->doValidateForm('password', ['password' => $password, 'is_pwd' => $isPwd]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['admin_pwd'] = $password;
        }
        if ('add' == $type || null !== $password) {
            $result = $this->doValidateForm('password_confirmation', [
                'password'              => $password,
                'password_confirmation' => $passwordConfirmation,
                'is_pwd'                => $isPwd,
            ]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['group_id'] = $groupId;
        }
        if ('add' == $type || null !== $privilege) {
            $result = $this->doValidateForm('privilege', $privilege);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['privilege'] = $privilege;

        }

        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;

        }

        //最高级管理不检查该项 管理员可否被当前管理员添加编辑
        if (1 != session('backend_info.id')) {
            $mFindAllow = Model\AdminGroups::mFindAllow();
            if (!mInArray($groupId, $mFindAllow)) {
                return $this->error(trans('common.you') . trans('common.none') . trans('common.privilege'));
            }

        }

        return $data;
    }

    //构造管理员assign公共数据
    private function addEditCommon()
    {
        $assign['privilege'] = $this->getPrivilege('Admin', session('backend_info.privilege'));
        View::share($assign);
    }
}
