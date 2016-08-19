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
        $v_value                       = '';
        $v_value                       = M_mktime_range('add_time');
        $v_value && $where['add_time'] = $v_value;
        $v_value                       = I('admin_id');
        $v_value && $where['admin_id'] = array(
            'in',
            $AdminModel->where(array('admin_name' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );
        $v_value                              = I('controller_name');
        $v_value && $where['controller_name'] = $v_value;

        //初始化翻页 和 列表数据
        $admin_log_list = $AdminLogModel->m_select($where, true);
        foreach ($admin_log_list as &$admin_log) {
            $admin_log['admin_name'] = $AdminModel->m_find_column($admin_log['admin_id'], 'admin_name');
        }
        $this->assign('admin_log_list', $admin_log_list);
        $this->assign('admin_log_list_count', $AdminLogModel->get_page_count($where));

        //初始化where_info
        $where_info                    = array();
        $where_info['add_time']        = array('type' => 'time', 'name' => L('add') . L('time'));
        $where_info['admin_id']        = array('type' => 'input', 'name' => L('admin') . L('name'));
        $where_info['controller_name'] = array('type' => 'input', 'name' => L('controller') . L('name'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle        = array();
        $batch_handle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

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
        $result_del    = $AdminLogModel->m_del($id);
        if ($result_del) {
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
        $result_del    = $AdminLogModel->m_del_all();
        if ($result_del) {
            $this->success(L('log') . L('del') . L('success'), U('index'));
        } else {
            $this->error(L('log') . L('del') . L('error'), U('index'));
        }
    }

}
