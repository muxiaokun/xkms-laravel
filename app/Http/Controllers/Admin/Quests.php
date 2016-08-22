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
// 后台 问卷调查

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Quests extends Backend
{
    //列表
    public function index()
    {
        $where = array();

        //建立where
        $whereValue                         = '';
        $whereValue                         = I('title');
        $whereValue && $where['title']      = array('like', '%' . $whereValue . '%');
        $whereValue                         = mMktimeRange('start_time');
        $whereValue && $where['start_time'] = $whereValue;
        $whereValue                         = mMktimeRange('end_time');
        $whereValue && $where['end_time']   = $whereValue;

        $QuestsModel = D('Quests');
        $questsList = $QuestsModel->mSelect($where, true);
        $this->assign('quests_list', $questsList);
        $this->assign('quests_list_count', $QuestsModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = array();
        $whereInfo['title']      = array('type' => 'input', 'name' => L('title'));
        $whereInfo['start_time'] = array('type' => 'time', 'name' => L('start') . L('time'));
        $whereInfo['end_time']   = array('type' => 'time', 'name' => L('end') . L('time'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle                 = array();
        $batchHandle['add']          = $this->_check_privilege('add');
        $batchHandle['answer_index'] = $this->_check_privilege('index', 'QuestsAnswer');
        $batchHandle['answer_edit']  = $this->_check_privilege('edit', 'QuestsAnswer');
        $batchHandle['edit']         = $this->_check_privilege('edit');
        $batchHandle['del']          = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('quests') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $QuestsModel = D('Quests');
            $data        = $this->makeData();
            $resultAdd  = $QuestsModel->mAdd($data);
            if ($resultAdd) {
                $this->success(L('quests') . L('add') . L('success'), U('Quests/index'));
                return;
            } else {
                $this->error(L('quests') . L('add') . L('error'), U('Quests/add'));
            }
        }
        $this->assign('title', L('add') . L('quests'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $QuestsModel = D('Quests');
        if (IS_POST) {
            $data        = $this->makeData();
            $resultEdit = $QuestsModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(L('quests') . L('edit') . L('success'), U('Quests/index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('quests') . L('edit') . L('error'), $errorGoLink);
            }
        }
        $editInfo = $QuestsModel->mFind($id);
        $this->assign('edit_info', $editInfo);

        $this->assign('title', L('edit') . L('quests'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('Quests/index'));
        }

        $clear       = I('clear');
        $QuestsModel = D('Quests');
        if (!$clear) {
            $resultDel = $QuestsModel->mDel($id);
        }

        if ($resultDel || $clear) {
            //删除问卷会删除该问卷下的所有答案
            $QuestsAnswerModel = D('QuestsAnswer');
            //TODO 需要定义数据列
            $resultClear      = $QuestsAnswerModel->mClean($id);
            if ($clear) {
                if ($resultClear) {
                    $QuestsModel->where(array('id' => $id))->data(array('current_portion' => 0))->save();
                    $this->success(L('quests') . L('clear') . L('success'), U('Quests/index'));
                    return;
                } else {
                    $this->error(L('quests') . L('clear') . L('error'), U('Quests/index'));

                }
            }
            $this->success(L('quests') . L('del') . L('success'), U('Quests/index'));
            return;
        } else {
            $this->error(L('quests') . L('del') . L('error'), U('Quests/edit', array('id' => $id)));
        }
    }

    //构造数据
    private function makeData()
    {
        $title         = I('title');
        $maxPortion   = I('max_portion');
        $startTime    = I('start_time');
        $endTime      = I('end_time');
        $startTime    = mMktime($startTime, true);
        $endTime      = mMktime($endTime, true);
        $startContent = I('start_content');
        $endContent   = I('end_content');
        $accessInfo   = I('access_info');
        $extInfo      = I('ext_info');
        foreach ($extInfo as &$info) {
            $info = htmlspecialchars_decode($info);
            $info = json_decode($info, true);
        }
        $extInfo = json_encode($extInfo);

        $data                                                                       = array();
        ('add' == ACTION_NAME || null !== $title) && $data['title']                 = $title;
        ('add' == ACTION_NAME || null !== $maxPortion) && $data['max_portion']     = $maxPortion;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time']       = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time']           = $endTime;
        ('add' == ACTION_NAME || null !== $startContent) && $data['start_content'] = $startContent;
        ('add' == ACTION_NAME || null !== $endContent) && $data['end_content']     = $endContent;
        ('add' == ACTION_NAME || null !== $accessInfo) && $data['access_info']     = $accessInfo;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info']           = $extInfo;
        return $data;
    }
}
