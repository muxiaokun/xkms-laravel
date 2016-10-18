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
        //初始化页面参数
        $where         = [];

        //建立where
        $whereValue = '';
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where['add_time'] = $whereValue;
        $whereValue = request('admin_id');
        $whereValue && $where['admin_id'] = [
            'in',
            Model\Admins::where(['admin_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = request('controller_name');
        $whereValue && $where['controller_name'] = $whereValue;

        //初始化翻页 和 列表数据
        $adminLogList = Model\AdminLog::mSelect($where, true);
        foreach ($adminLogList as &$adminLog) {
            $adminLog['admin_name'] = Model\Admins::mFindColumn($adminLog['admin_id'], 'admin_name');
        }
        $assign['admin_log_list']       = $adminLogList;
        $assign['admin_log_list_count'] = Model\AdminLog::mGetPageCount($where);

        //初始化where_info
        $whereInfo                    = [];
        $whereInfo['add_time']        = ['type' => 'time', 'name' => trans('common.add') . trans('common.time')];
        $whereInfo['admin_id']        = ['type' => 'input', 'name' => trans('common.admin') . trans('common.name')];
        $whereInfo['controller_name'] = [
            'type' => 'input',
            'name' => trans('common.controller') . trans('common.name'),
        ];
        $assign['where_info']         = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.admin') . trans('common.log') . trans('common.management');
        return view('admin.', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\AdminLog::mDel($id);
        if ($resultDel) {
            return $this->success(trans('common.log') . trans('common.del') . trans('common.success'), route('index'));
        } else {
            return $this->error(trans('common.log') . trans('common.del') . trans('common.error'), route('index'));
        }
    }

    //清除全部日志
    public function del_all()
    {
        if (session('backend_info.id') != 1) {
            return $this->error('only ROOT privilege', route('index'));
        }

        $resultDel = Model\AdminLog::mDel_all();
        if ($resultDel) {
            return $this->success(trans('common.log') . trans('common.del') . trans('common.success'), route('index'));
        } else {
            return $this->error(trans('common.log') . trans('common.del') . trans('common.error'), route('index'));
        }
    }

}
