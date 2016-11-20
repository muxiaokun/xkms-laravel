<?php
// 后台 考核

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Assess extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('title');
        $whereValue && $where['title'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('group_level');
        $whereValue && $where[] = [
            'group_level',
            Model\MemberGroup::where(['name', 'like', '%' . $whereValue . '%'])->select(['id'])->pluck('id'),
        ];
        $whereValue = mMktimeRange('start_time');
        $whereValue && $where[] = ['start_time', $whereValue];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $assessList            = Model\Assess::where($where)->ordered()->paginate(config('system.sys_max_row'));
        $assign['assess_list'] = $assessList;

        //初始化where_info
        $whereInfo                = [];
        $whereInfo['title']       = ['type' => 'input', 'name' => trans('common.title')];
        $whereInfo['group_level'] = ['type' => 'input', 'name' => trans('common.assess') . trans('common.group')];
        $whereInfo['start_time']  = ['type' => 'time', 'name' => trans('common.add') . trans('common.time')];
        $whereInfo['is_enable']   = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
            'value' => [1 => trans('common.enable'), 2 => trans('common.disable')],
        ];
        $assign['where_info']     = $whereInfo;

        //初始化batch_handle
        $batchHandle             = [];
        $batchHandle['add']      = $this->_check_privilege('add');
        $batchHandle['edit']     = $this->_check_privilege('edit');
        $batchHandle['log_edit'] = $this->_check_privilege('edit', 'AssessLog');
        $batchHandle['del']      = $this->_check_privilege('del');
        $assign['batch_handle']  = $batchHandle;

        $assign['title'] = trans('common.assess') . trans('common.management');
        return view('admin.Assess_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData();
            $resultAdd = Model\Assess::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.assess') . trans('common.add') . trans('common.success'),
                    route('Admin::Assess::index'));
            } else {
                return $this->error(trans('common.assess') . trans('common.add') . trans('common.error'),
                    route('Admin::Assess::add'));
            }
        }

        $assign['title'] = trans('common.assess') . trans('common.add');
        return view('admin.Assess_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Assess::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData();
            $resultEdit = Model\Assess::idWhere($id)->update($data);
            if ($resultEdit) {
                return $this->success(trans('common.assess') . trans('common.edit') . trans('common.success'),
                    route('Admin::Assess::index'));

            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Assess::index') : route('Admin::Assess::edit',
                    ['id' => $id]);
                return $this->error(trans('common.assess') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo               = Model\Assess::where('id', $id)->first();
        $editInfo['group_name'] = Model\MemberGroup::idWhere($editInfo['group_level'])->first()['name'];
        $assign['edit_info']    = $editInfo;

        $assign['title'] = trans('common.assess') . trans('common.edit');
        return view('admin.Assess_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Assess::index'));
        }

        $resultDel = Model\Assess::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.assess') . trans('common.del') . trans('common.success'),
                route('Admin::Assess::index'));

        } else {
            return $this->error(trans('common.assess') . trans('common.del') . trans('common.error'),
                route('Admin::Assess::index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'group_level':
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
    private function makeData()
    {
        //初始化参数
        $id           = request('get.id');
        $title        = request('title');
        $explains     = request('explains');
        $groupLevel   = request('group_level');
        $startTime    = request('start_time');
        $endTime      = request('end_time');
        $startTime    = mMktime($startTime, true);
        $endTime      = mMktime($endTime, true);
        $isEnable     = request('is_enable');
        $target       = request('target');
        $extInfo      = [];
        $gradeProject = [];
        foreach (request('ext_info') as $value) {
            $gradeProject[] = json_decode(str_replace('&quot;', '"', $value), true);
        }

        $data = [];
        ('add' == ACTION_NAME || null !== $title) && $data['title'] = $title;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains'] = $explains;
        ('add' == ACTION_NAME || null !== $groupLevel) && $data['group_level'] = $groupLevel;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time'] = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time'] = $endTime;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;
        ('add' == ACTION_NAME || null !== $target) && $data['target'] = $target;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info'] = $extInfo;

        return $data;
    }
}
