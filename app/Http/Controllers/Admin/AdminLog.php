<?php
// 后台 管理员日志

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class AdminLog extends Backend
{
    //列表
    public function index()
    {
        //初始化页面参数
        $AdminModel    = D('Admin');
        $AdminLogModel = D('AdminLog');
        $where         = [];

        //建立where
        $whereValue = '';
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where['add_time'] = $whereValue;
        $whereValue = request('admin_id');
        $whereValue && $where['admin_id'] = [
            'in',
            $AdminModel->where(['admin_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = request('controller_name');
        $whereValue && $where['controller_name'] = $whereValue;

        //初始化翻页 和 列表数据
        $adminLogList = $AdminLogModel->mSelect($where, true);
        foreach ($adminLogList as &$adminLog) {
            $adminLog['admin_name'] = $AdminModel->mFindColumn($adminLog['admin_id'], 'admin_name');
        }
        $this->assign('admin_log_list', $adminLogList);
        $this->assign('admin_log_list_count', $AdminLogModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                    = [];
        $whereInfo['add_time']        = ['type' => 'time', 'name' => trans('add') . trans('time')];
        $whereInfo['admin_id']        = ['type' => 'input', 'name' => trans('admin') . trans('name')];
        $whereInfo['controller_name'] = ['type' => 'input', 'name' => trans('controller') . trans('name')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle        = [];
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('admin') . trans('log') . trans('management'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $AdminLogModel = D('AdminLog');
        $resultDel     = $AdminLogModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('log') . trans('del') . trans('success'), route('index'));
        } else {
            $this->error(trans('log') . trans('del') . trans('error'), route('index'));
        }
    }

    //清除全部日志
    public function del_all()
    {
        if (session('backend_info.id') != 1) {
            $this->error('only ROOT privilege', route('index'));
        }

        $AdminLogModel = D('AdminLog');
        $resultDel     = $AdminLogModel->mDel_all();
        if ($resultDel) {
            $this->success(trans('log') . trans('del') . trans('success'), route('index'));
        } else {
            $this->error(trans('log') . trans('del') . trans('error'), route('index'));
        }
    }

}
