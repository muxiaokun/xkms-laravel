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
        $whereValue                         = '';
        $whereValue                         = I('name');
        $whereValue && $where['name']       = array('like', '%' . $whereValue . '%');
        $whereValue                         = M_mktime_range('start_time');
        $whereValue && $where['start_time'] = $whereValue;
        $whereValue                         = M_mktime_range('end_time');
        $whereValue && $where['end_time']   = $whereValue;
        //初始化翻页 和 列表数据
        $recruitList = $RecruitModel->mSelect($where, true);
        $this->assign('recruit_list', $recruitList);
        $this->assign('recruit_list_count', $RecruitModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = array();
        $whereInfo['name']       = array('type' => 'input', 'name' => L('recruit') . L('name'));
        $whereInfo['start_time'] = array('type' => 'time', 'name' => L('start') . L('time'));
        $whereInfo['end_time']   = array('type' => 'time', 'name' => L('end') . L('time'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle              = array();
        $batchHandle['log_index'] = $this->_check_privilege('add', 'RecruitLog');
        $batchHandle['add']       = $this->_check_privilege('add');
        $batchHandle['edit']      = $this->_check_privilege('edit');
        $batchHandle['del']       = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('recruit') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $RecruitModel = D('Recruit');
            $data         = $this->makeData();
            $resultAdd   = $RecruitModel->mAdd($data);
            if ($resultAdd) {
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
            $data        = $this->makeData();
            $resultEdit = $RecruitModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(L('recruit') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('recruit') . L('edit') . L('error'), $errorGoLink);
            }
        }

        $editInfo = $RecruitModel->mFind($id);
        $this->assign('edit_info', $editInfo);

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
        $resultDel   = $RecruitModel->mDel($id);
        if ($resultDel) {
            $RecruitLogModel = D('RecruitLog');
            //TODO 需要定义数据列
            $resultDel      = $RecruitLogModel->mClean($id);
            $this->success(L('recruit') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('recruit') . L('del') . L('error'), U('index'));
        }
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $title           = I('title');
        $explains        = I('explains');
        $isEnable       = I('is_enable');
        $currentPortion = I('current_portion');
        $maxPortion     = I('max_portion');
        $startTime      = M_mktime(I('start_time'), true);
        $endTime        = M_mktime(I('end_time'), true);
        $extInfo        = I('ext_info');

        $data                                                                           = array();
        ('add' == ACTION_NAME || null !== $title) && $data['title']                     = $title;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains']               = $explains;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable']             = $isEnable;
        ('add' == ACTION_NAME || null !== $currentPortion) && $data['current_portion'] = $currentPortion;
        ('add' == ACTION_NAME || null !== $maxPortion) && $data['max_portion']         = $maxPortion;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time']           = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time']               = $endTime;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info']               = $extInfo;

        return $data;
    }
}
