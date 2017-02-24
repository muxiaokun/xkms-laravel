<?php
// 后台 考核

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Carbon\Carbon;

class Assess extends Backend
{
    //列表
    public function index()
    {
        //初始化翻页 和 列表数据
        $assessList              = Model\Assess::where(function ($query) {
            $title = request('title');
            if ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            $group_name = request('group_name');
            if ($group_name) {
                $memberGroupIds = Model\MemberGroup::where('name', 'like',
                    '%' . $group_name . '%')->select(['id'])->pluck('id');
                $query->whereIn('group_level', $memberGroupIds);
            }

            $start_time = mMktimeRange('start_time');
            if ($start_time) {
                $query->timeWhere('start_time', $start_time);
            }

            $is_enable = request('is_enable');
            if ($is_enable) {
                $query->where('is_enable', '=', (1 == $is_enable) ? 1 : 0);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($assessList as &$assess) {
            if ($assess['group_level']) {
                $memberInfo           = Model\MemberGroup::colWhere($assess['group_level'])->first();
                $assess['group_name'] = (null === $memberInfo) ? $assess['group_level'] : $memberInfo['name'];
            } else {
                $assess['group_name'] = trans('assess.assess') . trans('common.group') . trans('common.empty');
            }
        }
        $assign['assess_list'] = $assessList;

        //初始化where_info
        $whereInfo                = [];
        $whereInfo['title']       = ['type' => 'input', 'name' => trans('common.title')];
        $whereInfo['group_name'] = ['type' => 'input', 'name' => trans('assess.assess') . trans('common.group')];
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

        $assign['title'] = trans('assess.assess') . trans('common.management');
        return view('admin.Assess_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Assess::create($data);
            if ($resultAdd) {
                return $this->success(trans('assess.assess') . trans('common.add') . trans('common.success'),
                    route('Admin::Assess::index'));
            } else {
                return $this->error(trans('assess.assess') . trans('common.add') . trans('common.error'),
                    route('Admin::Assess::add'));
            }
        }

        $assign['edit_info'] = Model\Assess::columnEmptyData();
        $assign['title'] = trans('assess.assess') . trans('common.add');
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
            $data       = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = false;
            Model\Assess::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('assess.assess') . trans('common.edit') . trans('common.success'),
                    route('Admin::Assess::index'));

            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Assess::index') : route('Admin::Assess::edit',
                    ['id' => $id]);
                return $this->error(trans('assess.assess') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo               = Model\Assess::colWhere($id)->first()->toArray();
        $editInfo['group_name'] = Model\MemberGroup::colWhere($editInfo['group_level'])->first()['name'];
        $assign['edit_info']    = $editInfo;

        $assign['title'] = trans('assess.assess') . trans('common.edit');
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
            return $this->success(trans('assess.assess') . trans('common.del') . trans('common.success'),
                route('Admin::Assess::index'));

        } else {
            return $this->error(trans('assess.assess') . trans('common.del') . trans('common.error'),
                route('Admin::Assess::index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'group_level':
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
        $title        = request('title');
        $explains     = request('explains');
        $groupLevel   = request('group_level');
        $startTime    = request('start_time');
        $endTime      = request('end_time');
        $isEnable     = request('is_enable');
        $target       = request('target');
        $extInfo = request('ext_info');
        $gradeProject = [];
        if (is_array($extInfo)) {
            foreach ($extInfo as $value) {
                $gradeProject[] = json_decode(htmlspecialchars_decode($value), true);
            }
        }
        $extInfo = $gradeProject;

        $data = [];
        if ('add' == $type || null !== $title) {
            $data['title'] = $title;
        }
        if ('add' == $type || null !== $explains) {
            $data['explains'] = $explains;
        }
        if ('add' == $type || null !== $groupLevel) {
            $data['group_level'] = $groupLevel ? $groupLevel : 0;
        }
        if ('add' == $type || null !== $startTime) {
            $data['start_time'] = $startTime ? $startTime : Carbon::now();
        }
        if ('add' == $type || null !== $endTime) {
            $data['end_time'] = $endTime ? $endTime : Carbon::now();
        }
        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;
        }
        if ('add' == $type || null !== $target) {
            $data['target'] = $target;
        }
        if ('add' == $type || null !== $extInfo) {
            $data['ext_info'] = $extInfo;
        }

        return $data;
    }
}
