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
        //建立where
        $where      = [];
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where[] = ['add_time', $whereValue];
        $whereValue = request('admin_id');
        $whereValue && $where[] = [
            'admin_id',
            'In',
            Model\Admins::where([['admin_name', 'like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];
        $whereValue = request('route_name');
        $whereValue && $where[] = ['route_name', $whereValue];
        //初始化翻页 和 列表数据
        $adminLogList = Model\AdminLogs::where($where)->ordered()->paginate(config('system.sys_max_row'));
        foreach ($adminLogList as &$adminLog) {
            $adminLog['admin_name'] = Model\Admins::idWhere($adminLog['admin_id'])->first()['admin_name'];
        }
        $assign['admin_log_list']       = $adminLogList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['add_time']   = ['type' => 'time', 'name' => trans('common.add') . trans('common.time')];
        $whereInfo['admin_id']   = ['type' => 'input', 'name' => trans('common.admin') . trans('common.name')];
        $whereInfo['route_name'] = [
            'type' => 'input',
            'name' => trans('common.route') . trans('common.name'),
        ];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['del']     = $this->_check_privilege('del');
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

        $resultDel = Model\AdminLogs::destroy($id);
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

        $resultDel = Model\AdminLogs::truncate();
        if ($resultDel) {
            return $this->success(trans('common.log') . trans('common.del') . trans('common.success'),
                route('Admin::AdminLog::index'));
        } else {
            return $this->error(trans('common.log') . trans('common.del') . trans('common.error'),
                route('Admin::AdminLog::index'));
        }
    }

}
