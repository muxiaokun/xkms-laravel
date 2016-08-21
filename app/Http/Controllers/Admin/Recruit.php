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
// 后台 招聘

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Recruit extends Backend
{
    //列表
    public function index()
    {
        $RecruitModel = D('Recruit');
        //建立where
        $v_value                         = '';
        $v_value                         = I('name');
        $v_value && $where['name']       = array('like', '%' . $v_value . '%');
        $v_value                         = M_mktime_range('start_time');
        $v_value && $where['start_time'] = $v_value;
        $v_value                         = M_mktime_range('end_time');
        $v_value && $where['end_time']   = $v_value;
        //初始化翻页 和 列表数据
        $recruit_list = $RecruitModel->mSelect($where, true);
        $this->assign('recruit_list', $recruit_list);
        $this->assign('recruit_list_count', $RecruitModel->getPageCount($where));

        //初始化where_info
        $where_info               = array();
        $where_info['name']       = array('type' => 'input', 'name' => L('recruit') . L('name'));
        $where_info['start_time'] = array('type' => 'time', 'name' => L('start') . L('time'));
        $where_info['end_time']   = array('type' => 'time', 'name' => L('end') . L('time'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle              = array();
        $batch_handle['log_index'] = $this->_check_privilege('add', 'RecruitLog');
        $batch_handle['add']       = $this->_check_privilege('add');
        $batch_handle['edit']      = $this->_check_privilege('edit');
        $batch_handle['del']       = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('recruit') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $RecruitModel = D('Recruit');
            $data         = $this->_make_data();
            $result_add   = $RecruitModel->mAdd($data);
            if ($result_add) {
                $this->success(L('recruit') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('recruit') . L('add') . L('error'), U('add'));
            }
        }
        $this->assign('title', L('add') . L('recruit'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $RecruitModel = D('Recruit');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $RecruitModel->mEdit($id, $data);
            if ($result_edit) {
                $this->success(L('recruit') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('recruit') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info = $RecruitModel->mFind($id);
        $this->assign('edit_info', $edit_info);

        $this->assign('title', L('edit') . L('recruit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $RecruitModel = D('Recruit');
        $result_del   = $RecruitModel->mDel($id);
        if ($result_del) {
            $RecruitLogModel = D('RecruitLog');
            //TODO 需要定义数据列
            $result_del      = $RecruitLogModel->mClean($id);
            $this->success(L('recruit') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('recruit') . L('del') . L('error'), U('index'));
        }
    }

    //构造数据
    private function _make_data()
    {
        //初始化参数
        $title           = I('title');
        $explains        = I('explains');
        $is_enable       = I('is_enable');
        $current_portion = I('current_portion');
        $max_portion     = I('max_portion');
        $start_time      = M_mktime(I('start_time'), true);
        $end_time        = M_mktime(I('end_time'), true);
        $ext_info        = I('ext_info');

        $data                                                                           = array();
        ('add' == ACTION_NAME || null !== $title) && $data['title']                     = $title;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains']               = $explains;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable']             = $is_enable;
        ('add' == ACTION_NAME || null !== $current_portion) && $data['current_portion'] = $current_portion;
        ('add' == ACTION_NAME || null !== $max_portion) && $data['max_portion']         = $max_portion;
        ('add' == ACTION_NAME || null !== $start_time) && $data['start_time']           = $start_time;
        ('add' == ACTION_NAME || null !== $end_time) && $data['end_time']               = $end_time;
        ('add' == ACTION_NAME || null !== $ext_info) && $data['ext_info']               = $ext_info;

        return $data;
    }
}
