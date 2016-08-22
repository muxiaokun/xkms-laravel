<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
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
        $where         = array();

        //建立where
        $whereValue                       = '';
        $whereValue                       = mMktimeRange('add_time');
        $whereValue && $where['add_time'] = $whereValue;
        $whereValue                       = I('admin_id');
        $whereValue && $where['admin_id'] = array(
            'in',
            $AdminModel->where(array('admin_name' => array('like', '%' . $whereValue . '%')))->mColumn2Array('id'),
        );
        $whereValue                              = I('controller_name');
        $whereValue && $where['controller_name'] = $whereValue;

        //初始化翻页 和 列表数据
        $adminLogList = $AdminLogModel->mSelect($where, true);
        foreach ($adminLogList as &$adminLog) {
            $adminLog['admin_name'] = $AdminModel->mFindColumn($adminLog['admin_id'], 'admin_name');
        }
        $this->assign('admin_log_list', $adminLogList);
        $this->assign('admin_log_list_count', $AdminLogModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                    = array();
        $whereInfo['add_time']        = array('type' => 'time', 'name' => L('add') . L('time'));
        $whereInfo['admin_id']        = array('type' => 'input', 'name' => L('admin') . L('name'));
        $whereInfo['controller_name'] = array('type' => 'input', 'name' => L('controller') . L('name'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle        = array();
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('admin') . L('log') . L('management'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminLogModel = D('AdminLog');
        $resultDel    = $AdminLogModel->mDel($id);
        if ($resultDel) {
            $this->success(L('log') . L('del') . L('success'), U('index'));
        } else {
            $this->error(L('log') . L('del') . L('error'), U('index'));
        }
    }

    //清除全部日志
    public function del_all()
    {
        if (session('backend_info.id') != 1) {
            $this->error('only ROOT privilege', U('index'));
        }

        $AdminLogModel = D('AdminLog');
        $resultDel    = $AdminLogModel->mDel_all();
        if ($resultDel) {
            $this->success(L('log') . L('del') . L('success'), U('index'));
        } else {
            $this->error(L('log') . L('del') . L('error'), U('index'));
        }
    }

}
