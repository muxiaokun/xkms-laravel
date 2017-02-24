<?php
// 后台 招聘

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Carbon\Carbon;

class Recruit extends Backend
{
    //列表
    public function index()
    {
        //初始化翻页 和 列表数据
        $recruitList            = Model\Recruit::where(function ($query) {
            $name = request('title');
            if ($name) {
                $query->where('title', 'like', '%' . $name . '%');
            }

            $start_time = mMktimeRange('start_time');
            if ($start_time) {
                $query->timeWhere('start_time', $start_time);
            }

            $end_time = mMktimeRange('end_time');
            if ($end_time) {
                $query->timeWhere('end_time', $end_time);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['recruit_list'] = $recruitList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['title']     = ['type' => 'input', 'name' => trans('recruit.recruit') . trans('common.title')];
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
                return $this->success(trans('recruit.recruit') . trans('common.add') . trans('common.success'),
                    route('Admin::Recruit::index'));
            } else {
                return $this->error(trans('recruit.recruit') . trans('common.add') . trans('common.error'),
                    route('Admin::Recruit::add'));
            }
        }
        $assign['edit_info'] = Model\Recruit::columnEmptyData();
        $assign['title'] = trans('common.add') . trans('recruit.recruit');
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

            $resultEdit = false;
            Model\Recruit::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('recruit.recruit') . trans('common.edit') . trans('common.success'),
                    route('Admin::Recruit::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Recruit::index') : route('Admin::Recruit::edit',
                    ['id' => $id]);
                return $this->error(trans('recruit.recruit') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo            = Model\Recruit::colWhere($id)->first()->toArray();
        $assign['edit_info'] = $editInfo;

        $assign['title'] = trans('common.edit') . trans('recruit.recruit');
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
            return $this->success(trans('recruit.recruit') . trans('common.del') . trans('common.success'),
                route('Admin::Recruit::index'));
        } else {
            return $this->error(trans('recruit.recruit') . trans('common.del') . trans('common.error'),
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
        $startTime = request('start_time');
        $endTime = request('end_time');
        $extInfo        = request('ext_info');

        $data = [];
        if ('add' == $type || null !== $title) {
            $data['title'] = $title;
        }
        if ('add' == $type || null !== $explains) {
            $data['explains'] = $explains;
        }
        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;
        }
        if ('add' == $type || null !== $currentPortion) {
            $data['current_portion'] = $currentPortion ? $currentPortion : 0;
        }
        if ('add' == $type || null !== $maxPortion) {
            $data['max_portion'] = $maxPortion ? $maxPortion : 0;
        }
        if ('add' == $type || null !== $startTime) {
            $data['start_time'] = $startTime ? $startTime : Carbon::now();
        }
        if ('add' == $type || null !== $endTime) {
            $data['end_time'] = $endTime ? $endTime : Carbon::now();
        }
        if ('add' == $type || null !== $extInfo) {
            $data['ext_info'] = $extInfo ? $extInfo : [];
        }

        return $data;
    }
}
