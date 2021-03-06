<?php
// 后台 管理员日志

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class AdminLog extends Backend
{
    //列表
    public function index()
    {

        //初始化翻页 和 列表数据
        $adminLogList = Model\AdminLog::where(function ($query) {
            $last_time = mMktimeRange('created_at');
            if ($last_time) {
                $query->timeWhere('created_at', $last_time);
            }

            $admin_id = request('admin_id');
            if ($admin_id) {
                $ids = Model\Admin::where([
                    [
                        'admin_name',
                        'like',
                        '%' . $admin_id . '%',
                    ],
                ])->select(['id'])->pluck('id');
                $query->whereIn('admin_id', $ids);
            }

            $route_name = request('route_name');
            if ($route_name) {
                $query->where('route_name', 'like', '%' . $route_name . '%');
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($adminLogList as &$adminLog) {
            $adminLog['admin_name'] = Model\Admin::colWhere($adminLog['admin_id'])->get()->implode('admin_name', ' | ');
        }
        $assign['admin_log_list'] = $adminLogList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['created_at'] = ['type' => 'time', 'name' => trans('common.add') . trans('common.time')];
        $whereInfo['admin_id']   = ['type' => 'input', 'name' => trans('common.admin') . trans('common.name')];
        $whereInfo['route_name'] = [
            'type' => 'input',
            'name' => trans('common.route') . trans('common.name'),
        ];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['del']     = $this->_check_privilege('Admin::AdminLog::del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.admin') . trans('common.log') . trans('common.management');
        return view('admin.AdminLog_index', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::AdminLog::index'));
        }

        $resultDel = Model\AdminLog::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.log') . trans('common.del') . trans('common.success'),
                route('Admin::AdminLog::index'));
        } else {
            return $this->error(trans('common.log') . trans('common.del') . trans('common.error'),
                route('Admin::AdminLog::index'));
        }
    }

    //清除全部日志
    public function del_all()
    {
        if (session('backend_info.id') != 1) {
            return $this->error('only ROOT privilege', route('Admin::AdminLog::index'));
        }

        $resultDel = Model\AdminLog::truncate();
        if ($resultDel) {
            return $this->success(trans('common.log') . trans('common.del') . trans('common.success'),
                route('Admin::AdminLog::index'));
        } else {
            return $this->error(trans('common.log') . trans('common.del') . trans('common.error'),
                route('Admin::AdminLog::index'));
        }
    }

}
