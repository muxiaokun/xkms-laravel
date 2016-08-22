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
// 后台 问卷调查答案

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class QuestsAnswer extends Backend
{
    //列表
    public function index()
    {
        $QuestsModel = D('Quests');
        $MemberModel = D('Member');
        $where       = array();

        //建立where
        $whereValue                        = '';
        $whereValue                        = I('quests_id');
        $whereValue && $where['quests_id'] = $whereValue;
        $whereValue                        = I('quests_title');
        $whereValue && $where['quests_id'] = array(
            'in',
            $QuestsModel->where(array('title' => array('like', '%' . $whereValue . '%')))->mColumn2Array('id'),
        );
        $whereValue                        = I('member_id');
        $whereValue && $where['member_id'] = array(
            'in',
            $MemberModel->where(array('member_name' => array('like', '%' . $whereValue . '%')))->mColumn2Array('id'),
        );

        $QuestsAnswerModel  = D('QuestsAnswer');
        $questsAnswerList = $QuestsAnswerModel->mSelect($where, true);
        foreach ($questsAnswerList as &$questsAnswer) {
            $memberName                  = $MemberModel->mFindColumn($groupId, 'name');
            $questsAnswer['member_name'] = ($memberName) ? $memberName : L('anonymous');
        }
        $this->assign('quests_answer_list', $questsAnswerList);
        $this->assign('quests_answer_list_count', $QuestsAnswerModel->mGetPageCount($where));
        //初始化where_info
        $whereInfo                 = array();
        $whereInfo['quests_title'] = array('type' => 'input', 'name' => L('quests') . L('name'));
        $whereInfo['member_id']    = array('type' => 'input', 'name' => L('member') . L('name'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle        = array();
        $batchHandle['add'] = $this->_check_privilege('add');
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('quests') . L('answer') . L('management'));
        $this->display();
    }

    //显示问卷答案
    public function add()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $QuestsModel        = D('Quests');
        $QuestsAnswerModel  = D('QuestsAnswer');
        $questsAnswerInfo = $QuestsAnswerModel->mFind($id);
        $questsInfo        = $QuestsModel->mFind($questsAnswerInfo['quests_id']);

        //初始化问题
        $questsQuestList = json_decode($questsInfo['ext_info'], true);
        foreach ($questsQuestList as $questId => $quest) {
            $questsQuestList[$questId]['answer'] = explode('|', $questsQuestList[$questId]['answer']);
        }
        $questsAnswerList = array();
        foreach (explode('|', $questsAnswerInfo['answer']) as $questAnswer) {
            list($key, $value)                        = explode(':', $questAnswer);
            '' != $key && $questsAnswerList[$key][] = $value;
        }

        $this->assign('quests_quest_list', $questsQuestList);
        $this->assign('quests_answer_list', $questsAnswerList);
        $this->assign('quests_info', $questsInfo);
        $this->assign('quests_answer_info', $questsAnswerInfo);
        $this->assign('title', L('quests') . L('answer'));
        $this->display();
    }

    //统计问卷答案
    public function edit()
    {
        $questsId = I('quests_id');
        if (!$questsId) {
            $this->error(L('id') . L('error'), U('index'));
        }

        //读取问题
        $QuestsModel = D('Quests');
        $questsInfo = $QuestsModel->mFind($questsId);
        //初始化问题
        $questsQuestList = json_decode($questsInfo['ext_info'], true);
        $QuestsAnswerModel = D('QuestsAnswer');
        foreach ($questsQuestList as $questId => $quest) {
            $answerName = explode('|', $questsQuestList[$questId]['answer']);
            $answer      = array();
            switch ($quest['answer_type']) {
                case 'radio':
                    foreach ($answerName as $k => $name) {
                        $answer[$k]['name']  = $name;
                        $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($questsId, '%|' . $questId . ':' . $k . "|%");
                    }
                    break;
                case 'checkbox':
                    foreach ($answerName as $k => $name) {
                        $answer[$k]['name']  = $name;
                        $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($questsId, '%|' . $questId . ':' . $k . "|%");
                    }
                    break;
                case 'text':
                    $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($questsId, "%|" . $questId . ":%");
                    break;
                case 'textarea':
                    $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($questsId, "%|" . $questId . ":%");
                    break;
            }
            $questsQuestList[$questId]['answer']    = $answer;
            $questsQuestList[$questId]['max_count'] = $QuestsAnswerModel->count_quests_answer($questsId);
        }
        $this->assign('quests_quest_list', $questsQuestList);

        $this->assign('title', L('quests') . L('answer') . L('statistics'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $QuestsAnswerModel  = D('QuestsAnswer');
        $questsAnswerInfo = $QuestsAnswerModel->mFind($id);
        $resultDel         = $QuestsAnswerModel->mDel($questsAnswerInfo['id']);
        if ($resultDel) {
            $QuestsModel = D('Quests');
            $QuestsModel->where(array('id' => $questsAnswerInfo['quests_id']))->setDec('current_portion');
            $this->success(L('del') . L('answer') . L('success'), U('index'));
        } else {
            $this->error(L('del') . L('answer') . L('error'), U('index'));
        }
    }
}
