<?php
// 后台 问卷调查答案

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class QuestsAnswer extends Backend
{
    //列表
    public function index()
    {
        $where = [];

        //建立where
        $whereValue = '';
        $whereValue = request('quests_id');
        $whereValue && $where[] = ['quests_id', $whereValue];
        $whereValue = request('quests_title');
        $whereValue && $where[] = [
            'quests_id',
            'in',
            Model\Quests::where(['title' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = request('member_id');
        $whereValue && $where[] = [
            'member_id',
            'in',
            Model\Member::where(['member_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];

        $questsAnswerList = Model\QuestsAnswer::mSelect($where, true);
        foreach ($questsAnswerList as &$questsAnswer) {
            $memberName                  = Model\Member::mFindColumn($groupId, 'name');
            $questsAnswer['member_name'] = ($memberName) ? $memberName : trans('common.anonymous');
        }
        $assign['quests_answer_list']       = $questsAnswerList;
        $assign['quests_answer_list_count'] = Model\QuestsAnswer::mGetPageCount($where);
        //初始化where_info
        $whereInfo                 = [];
        $whereInfo['quests_title'] = ['type' => 'input', 'name' => trans('common.quests') . trans('common.name')];
        $whereInfo['member_id']    = ['type' => 'input', 'name' => trans('common.member') . trans('common.name')];
        $assign['where_info']      = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.quests') . trans('common.answer') . trans('common.management');
        return view('admin.', $assign);
    }

    //显示问卷答案
    public function add()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $questsAnswerInfo = Model\QuestsAnswer::mFind($id);
        $questsInfo       = Model\Quests::mFind($questsAnswerInfo['quests_id']);

        //初始化问题
        $questsQuestList = json_decode($questsInfo['ext_info'], true);
        foreach ($questsQuestList as $questId => $quest) {
            $questsQuestList[$questId]['answer'] = explode('|', $questsQuestList[$questId]['answer']);
        }
        $questsAnswerList = [];
        foreach (explode('|', $questsAnswerInfo['answer']) as $questAnswer) {
            list($key, $value) = explode(':', $questAnswer);
            '' != $key && $questsAnswerList[$key][] = $value;
        }

        $assign['quests_quest_list']  = $questsQuestList;
        $assign['quests_answer_list'] = $questsAnswerList;
        $assign['quests_info']        = $questsInfo;
        $assign['quests_answer_info'] = $questsAnswerInfo;
        $assign['title']              = trans('common.quests') . trans('common.answer');
        return view('admin.', $assign);
    }

    //统计问卷答案
    public function edit()
    {
        $questsId = request('quests_id');
        if (!$questsId) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        //读取问题
        $questsInfo = Model\Quests::mFind($questsId);
        //初始化问题
        $questsQuestList = json_decode($questsInfo['ext_info'], true);
        foreach ($questsQuestList as $questId => $quest) {
            $answerName = explode('|', $questsQuestList[$questId]['answer']);
            $answer     = [];
            switch ($quest['answer_type']) {
                case 'radio':
                    foreach ($answerName as $k => $name) {
                        $answer[$k]['name']  = $name;
                        $answer[$k]['count'] = Model\QuestsAnswer::count_quests_answer($questsId,
                            '%|' . $questId . ':' . $k . "|%");
                    }
                    break;
                case 'checkbox':
                    foreach ($answerName as $k => $name) {
                        $answer[$k]['name']  = $name;
                        $answer[$k]['count'] = Model\QuestsAnswer::count_quests_answer($questsId,
                            '%|' . $questId . ':' . $k . "|%");
                    }
                    break;
                case 'text':
                    $answer[$k]['count'] = Model\QuestsAnswer::count_quests_answer($questsId, "%|" . $questId . ":%");
                    break;
                case 'textarea':
                    $answer[$k]['count'] = Model\QuestsAnswer::count_quests_answer($questsId, "%|" . $questId . ":%");
                    break;
            }
            $questsQuestList[$questId]['answer']    = $answer;
            $questsQuestList[$questId]['max_count'] = Model\QuestsAnswer::count_quests_answer($questsId);
        }
        $assign['quests_quest_list'] = $questsQuestList;

        $assign['title'] = trans('common.quests') . trans('common.answer') . trans('common.statistics');
        return view('admin.', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $questsAnswerInfo = Model\QuestsAnswer::mFind($id);
        $resultDel        = Model\QuestsAnswer::mDel($questsAnswerInfo['id']);
        if ($resultDel) {
            Model\Quests::where(['id' => $questsAnswerInfo['quests_id']])->setDec('current_portion');
            return $this->success(trans('common.del') . trans('common.answer') . trans('common.success'),
                route('index'));
        } else {
            return $this->error(trans('common.del') . trans('common.answer') . trans('common.error'), route('index'));
        }
    }
}
