<?php
// 后台 问卷调查

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Quests extends Backend
{
    //列表
    public function index()
    {
        $where = [];

        //建立where
        $whereValue = '';
        $whereValue = request('title');
        $whereValue && $where['title'] = ['like', '%' . $whereValue . '%'];
        $whereValue = mMktimeRange('start_time');
        $whereValue && $where['start_time'] = $whereValue;
        $whereValue = mMktimeRange('end_time');
        $whereValue && $where['end_time'] = $whereValue;

        $QuestsModel = D('Quests');
        $questsList  = $QuestsModel->mSelect($where, true);
        $this->assign('quests_list', $questsList);
        $this->assign('quests_list_count', $QuestsModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['title']      = ['type' => 'input', 'name' => trans('title')];
        $whereInfo['start_time'] = ['type' => 'time', 'name' => trans('start') . trans('time')];
        $whereInfo['end_time']   = ['type' => 'time', 'name' => trans('end') . trans('time')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle                 = [];
        $batchHandle['add']          = $this->_check_privilege('add');
        $batchHandle['answer_index'] = $this->_check_privilege('index', 'QuestsAnswer');
        $batchHandle['answer_edit']  = $this->_check_privilege('edit', 'QuestsAnswer');
        $batchHandle['edit']         = $this->_check_privilege('edit');
        $batchHandle['del']          = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('quests') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $QuestsModel = D('Quests');
            $data        = $this->makeData();
            $resultAdd   = $QuestsModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('quests') . trans('add') . trans('success'), route('Quests/index'));
                return;
            } else {
                $this->error(trans('quests') . trans('add') . trans('error'), route('Quests/add'));
            }
        }
        $this->assign('title', trans('add') . trans('quests'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $QuestsModel = D('Quests');
        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = $QuestsModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('quests') . trans('edit') . trans('success'), route('Quests/index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('quests') . trans('edit') . trans('error'), $errorGoLink);
            }
        }
        $editInfo = $QuestsModel->mFind($id);
        $this->assign('edit_info', $editInfo);

        $this->assign('title', trans('edit') . trans('quests'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('Quests/index'));
        }

        $clear       = request('clear');
        $QuestsModel = D('Quests');
        if (!$clear) {
            $resultDel = $QuestsModel->mDel($id);
        }

        if ($resultDel || $clear) {
            //删除问卷会删除该问卷下的所有答案
            $QuestsAnswerModel = D('QuestsAnswer');
            //TODO 需要定义数据列
            $resultClear = $QuestsAnswerModel->mClean($id);
            if ($clear) {
                if ($resultClear) {
                    $QuestsModel->where(['id' => $id])->data(['current_portion' => 0])->save();
                    $this->success(trans('quests') . trans('clear') . trans('success'), route('Quests/index'));
                    return;
                } else {
                    $this->error(trans('quests') . trans('clear') . trans('error'), route('Quests/index'));

                }
            }
            $this->success(trans('quests') . trans('del') . trans('success'), route('Quests/index'));
            return;
        } else {
            $this->error(trans('quests') . trans('del') . trans('error'), route('Quests/edit', ['id' => $id]));
        }
    }

    //构造数据
    private function makeData()
    {
        $title        = request('title');
        $maxPortion   = request('max_portion');
        $startTime    = request('start_time');
        $endTime      = request('end_time');
        $startTime    = mMktime($startTime, true);
        $endTime      = mMktime($endTime, true);
        $startContent = request('start_content');
        $endContent   = request('end_content');
        $accessInfo   = request('access_info');
        $extInfo      = request('ext_info');
        foreach ($extInfo as &$info) {
            $info = htmlspecialchars_decode($info);
            $info = json_decode($info, true);
        }
        $extInfo = json_encode($extInfo);

        $data = [];
        ('add' == ACTION_NAME || null !== $title) && $data['title'] = $title;
        ('add' == ACTION_NAME || null !== $maxPortion) && $data['max_portion'] = $maxPortion;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time'] = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time'] = $endTime;
        ('add' == ACTION_NAME || null !== $startContent) && $data['start_content'] = $startContent;
        ('add' == ACTION_NAME || null !== $endContent) && $data['end_content'] = $endContent;
        ('add' == ACTION_NAME || null !== $accessInfo) && $data['access_info'] = $accessInfo;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info'] = $extInfo;
        return $data;
    }
}
