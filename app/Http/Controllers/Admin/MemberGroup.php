<?php
// 后台 会员组

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class MemberGroup extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $memberGroupList = Model\MemberGroup::where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['member_group_list'] = $memberGroupList;

        //初始化where_info
        $whereInfo              = [];
        $whereInfo['name']      = ['type' => 'input', 'name' => trans('common.group') . trans('common.name')];
        $whereInfo['is_enable'] = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
            'value' => [1 => trans('common.enable'), 2 => trans('common.disable')],
        ];
        $assign['where_info']   = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.member') . trans('common.group') . trans('common.management');
        return view('admin.MemberGroup_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\MemberGroup::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.member') . trans('common.group') . trans('common.add') . trans('common.success'),
                    route('Admin::MemberGroup::index'));
            } else {
                return $this->error(trans('common.member') . trans('common.group') . trans('common.add') . trans('common.error'),
                    route('Admin::MemberGroup::add'));
            }
        }

        $this->addEditCommon();
        $assign['edit_info'] = Model\MemberGroup::columnEmptyData();
        $assign['title']     = trans('common.member') . trans('common.group') . trans('common.add');
        return view('admin.MemberGroup_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::MemberGroup::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            //root组只能修改 名和说明
            if (1 == $id || (is_array($id) && in_array(1, $id))) {
                $root_data = [];
                if (isset($data['name'])) {
                    $root_data['name'] = $data['name'];
                }
                if (isset($data['explains'])) {
                    $root_data['explains'] = $data['explains'];
                }
                $data = $root_data;
            }

            $resultEdit = false;
            Model\MemberGroup::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('common.member') . trans('common.group') . trans('common.edit') . trans('common.success'),
                    route('Admin::MemberGroup::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::MemberGroup::index') : route('Admin::MemberGroup::edit',
                    ['id' => $id]);
                return $this->error(trans('common.member') . trans('common.group') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }
        //获取分组默认信息
        $editInfo = Model\MemberGroup::colWhere($id)->first()->toArray();
        foreach ($editInfo['manage_id'] as $manageKey => $manageId) {
            $memberName                        = Model\Member::colWhere($manageId)->first()['member_name'];
            $editInfo['manage_id'][$manageKey] = ['value' => $manageId, 'html' => $memberName];
        }
        $assign['edit_info'] = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.member') . trans('common.group') . trans('common.edit');
        return view('admin.MemberGroup_addedit', $assign);
    }

    //删除
    public function del()
    {

        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::MemberGroup::index'));
        }

        if (1 == $id || (is_array($id) && in_array(1, $id))) {
            return $this->error('root' . trans('common.not') . trans('common.del'), route('Admin::Admin::index'));
        }

        $resultDel = false;
        if (1 != $id || (is_array($id) && !in_array(1, $id))) {
            $resultDel = Model\MemberGroup::destroy($id);
        }
        if ($resultDel) {
            //删除成功后 删除管理员与组的关系
            Model\Member::colWhere($id, 'group_id')->delete();
            return $this->success(trans('common.member') . trans('common.group') . trans('common.del') . trans('common.success'),
                route('Admin::MemberGroup::index'));
        } else {
            return $this->error(trans('common.member') . trans('common.group') . trans('common.del') . trans('common.error'),
                route('Admin::MemberGroup::index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'name':
                $validator = Validator::make($data, [
                    'name' => 'user_name|member_group_exist',
                ]);
                break;
            case 'privilege':
                $validator = Validator::make($data, [
                    'privilege' => 'privilege:frontend_info',
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
            case 'manage_id':
                Model\Member::where(function ($query) use ($data) {
                    if (isset($data['inserted']) && $data['inserted']) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword']) && $data['keyword']) {
                        $query->where('member_name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['member_name']];
                });
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $id        = request('id');
        $manageId  = request('manage_id');
        $name      = request('name');
        $explains  = request('explains');
        $privilege = request('privilege');
        $isEnable  = request('is_enable');

        //检测初始化参数是否合法
        if ($id) {
            if (is_array($id)) {
                $errorGoLink = route('Admin::MemberGroup::index');
            } else {
                $errorGoLink = route('Admin::MemberGroup::edit', ['id' => $id]);
            }
        } else {
            $errorGoLink = route('Admin::MemberGroup::add');
        }

        $data      = [];
        if ('add' == $type || null !== $manageId) {
            $data['manage_id'] = $manageId;
        }
        if ('add' == $type || null !== $name) {
            $result = $this->doValidateForm('name', ['id' => $id, 'name' => $name]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }

            $data['name'] = $name;
        }
        if ('add' == $type || null !== $explains) {
            $data['explains'] = $explains;
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
        return $data;
    }

    //构造assign公共数据
    private function addEditCommon()
    {
        $assign['privilege'] = $this->getPrivilege('Home');
        View::share($assign);
    }
}
