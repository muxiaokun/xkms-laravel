<?php
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
        $whereValue                         = request('name');
        $whereValue && $where['name']       = array('like', '%' . $whereValue . '%');
        $whereValue                         = mMktimeRange('start_time');
        $whereValue && $where['start_time'] = $whereValue;
        $whereValue                         = mMktimeRange('end_time');
        $whereValue && $where['end_time']   = $whereValue;
        //初始化翻页 和 列表数据
        $recruitList = $RecruitModel->mSelect($where, true);
        $this->assign('recruit_list', $recruitList);
        $this->assign('recruit_list_count', $RecruitModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = array();
        $whereInfo['name']       = array('type' => 'input', 'name' => trans('recruit') . trans('name'));
        $whereInfo['start_time'] = array('type' => 'time', 'name' => trans('start') . trans('time'));
        $whereInfo['end_time']   = array('type' => 'time', 'name' => trans('end') . trans('time'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle              = array();
        $batchHandle['log_index'] = $this->_check_privilege('add', 'RecruitLog');
        $batchHandle['add']       = $this->_check_privilege('add');
        $batchHandle['edit']      = $this->_check_privilege('edit');
        $batchHandle['del']       = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('recruit') . trans('management'));
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
                $this->success(trans('recruit') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('recruit') . trans('add') . trans('error'), route('add'));
            }
        }
        $this->assign('title', trans('add') . trans('recruit'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $RecruitModel = D('Recruit');
        if (IS_POST) {
            $data        = $this->makeData();
            $resultEdit = $RecruitModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('recruit') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', array('id' => $id));
                $this->error(trans('recruit') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo = $RecruitModel->mFind($id);
        $this->assign('edit_info', $editInfo);

        $this->assign('title', trans('edit') . trans('recruit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $RecruitModel = D('Recruit');
        $resultDel   = $RecruitModel->mDel($id);
        if ($resultDel) {
            $RecruitLogModel = D('RecruitLog');
            //TODO 需要定义数据列
            $resultDel      = $RecruitLogModel->mClean($id);
            $this->success(trans('recruit') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('recruit') . trans('del') . trans('error'), route('index'));
        }
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $title           = request('title');
        $explains        = request('explains');
        $isEnable       = request('is_enable');
        $currentPortion = request('current_portion');
        $maxPortion     = request('max_portion');
        $startTime      = mMktime(request('start_time'), true);
        $endTime        = mMktime(request('end_time'), true);
        $extInfo        = request('ext_info');

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
