<?php
// 后台 招聘

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Recruit extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = mMktimeRange('start_time');
        $whereValue && $where[] = ['start_time', $whereValue];
        $whereValue = mMktimeRange('end_time');
        $whereValue && $where[] = ['end_time', $whereValue];
        //初始化翻页 和 列表数据
        $recruitList = Model\Recruit::where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['recruit_list'] = $recruitList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['name'] = ['type' => 'input', 'name' => trans('recruit.recruit') . trans('common.name')];
        $whereInfo['start_time'] = ['type' => 'time', 'name' => trans('common.start') . trans('common.time')];
        $whereInfo['end_time']   = ['type' => 'time', 'name' => trans('common.end') . trans('common.time')];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle              = [];
        $batchHandle['log_index'] = $this->_check_privilege('add', 'RecruitLog');
        $batchHandle['add']       = $this->_check_privilege('add');
        $batchHandle['edit']      = $this->_check_privilege('edit');
        $batchHandle['del']       = $this->_check_privilege('del');
        $assign['batch_handle']   = $batchHandle;

        $assign['title'] = trans('recruit.recruit') . trans('common.management');
        return view('admin.Recruit_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Recruit::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.recruit') . trans('common.add') . trans('common.success'),
                    route('Admin::Recruit::index'));
            } else {
                return $this->error(trans('common.recruit') . trans('common.add') . trans('common.error'),
                    route('Admin::Recruit::add'));
            }
        }
        $assign['edit_info'] = Model\Recruit::columnEmptyData();
        $assign['title']     = trans('common.add') . trans('common.recruit');
        return view('admin.Recruit_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Recruit::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = Model\Recruit::colWhere($id)->first()->update($data);
            if ($resultEdit) {
                return $this->success(trans('common.recruit') . trans('common.edit') . trans('common.success'),
                    route('Admin::Recruit::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Recruit::index') : route('Admin::Recruit::edit',
                    ['id' => $id]);
                return $this->error(trans('common.recruit') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo            = Model\Recruit::colWhere($id)->first()->toArray();
        $assign['edit_info'] = $editInfo;

        $assign['title'] = trans('common.edit') . trans('common.recruit');
        return view('admin.Recruit_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Recruit::index'));
        }

        $resultDel = Model\Recruit::destroy($id);
        if ($resultDel) {
            Model\RecruitLog::colWhere($id, 'r_id')->delete();
            return $this->success(trans('common.recruit') . trans('common.del') . trans('common.success'),
                route('Admin::Recruit::index'));
        } else {
            return $this->error(trans('common.recruit') . trans('common.del') . trans('common.error'),
                route('Admin::Recruit::index'));
        }
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $title          = request('title');
        $explains       = request('explains');
        $isEnable       = request('is_enable');
        $currentPortion = request('current_portion');
        $maxPortion     = request('max_portion');
        $startTime      = mMktime(request('start_time'), true);
        $endTime        = mMktime(request('end_time'), true);
        $extInfo        = request('ext_info');

        $data = [];
        if ('add' == $type || null !== $title) {
            $data['title'] = $title;
        }
        if ('add' == $type || null !== $explains) {
            $data['explains'] = mParseContent($explains);
        }
        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;
        }
        if ('add' == $type || null !== $currentPortion) {
            $data['current_portion'] = $currentPortion;
        }
        if ('add' == $type || null !== $maxPortion) {
            $data['max_portion'] = $maxPortion;
        }
        if ('add' == $type || null !== $startTime) {
            $data['start_time'] = $startTime;
        }
        if ('add' == $type || null !== $endTime) {
            $data['end_time'] = $endTime;
        }
        if ('add' == $type || null !== $extInfo) {
            $data['ext_info'] = $extInfo;
        }

        return $data;
    }
}
