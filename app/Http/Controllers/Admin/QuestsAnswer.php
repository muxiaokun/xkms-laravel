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
        $whereValue = request('member_id');
        $whereValue && $where[] = [
            'member_id',
            'in',
            Model\Member::where(['member_name' => ['like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];

        $questsAnswerList          = Model\QuestsAnswer::where(function ($query) {
            $quests_id = request('quests_id');
            if ($quests_id) {
                $query->where('quests_id', '=', $quests_id);
            }

            $quests_title = request('quests_title');
            if ($quests_title) {
                $ids = Model\Quests::where([
                    [
                        'title',
                        'like',
                        '%' . $quests_title . '%',
                    ],
                ])->select(['id'])->pluck('id');
                $query->whereIn('quests_id', $ids);
            }

            $member_name = request('member_name');
            if ($member_name) {
                $ids = Model\Member::where([
                    [
                        'member_name',
                        'like',
                        '%' . $member_name . '%',
                    ],
                ])->select(['id'])->pluck('id');
                $query->whereIn('member_id', $ids);
            }


        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($questsAnswerList as &$questsAnswer) {
            $questsAnswer['quests_title'] = Model\Quests::colWhere($questsAnswer['quests_id'])->get()->implode('title',
                ' | ');
            $memberInfo                   = Model\Member::colWhere($questsAnswer['member_id'])->first();
            if (null === $memberInfo) {
                $questsAnswer['member_name'] = trans('common.member') . trans('common.not') . trans('common.exists');
            } else {
                $questsAnswer['member_name'] = $memberInfo['member_name'];
            }

        }
        $assign['quests_answer_list'] = $questsAnswerList;
        //初始化where_info
        $whereInfo                 = [];
        $whereInfo['quests_title'] = ['type' => 'input', 'name' => trans('quests.quests') . trans('common.title')];
        $whereInfo['member_name']  = ['type' => 'input', 'name' => trans('common.member') . trans('common.name')];
        $assign['where_info']      = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('quests.quests') . trans('common.answer') . trans('common.management');
        return view('admin.QuestsAnswer_index', $assign);
    }

    //显示问卷答案
    public function add()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::QuestsAnswer::index'));
        }

        $questsAnswerInfo = Model\QuestsAnswer::colWhere($id)->first()->toArray();
        $questsInfo       = Model\Quests::colWhere($questsAnswerInfo['quests_id'])->first()->toArray();

        //初始化问题
        $questsQuestList = $questsInfo['ext_info'];
        foreach ($questsQuestList as $questId => $quest) {
            $questsQuestList[$questId]['answer'] = explode('|', $questsQuestList[$questId]['answer']);
        }
        $questsAnswerList = [];
        foreach (explode('|', $questsAnswerInfo['answer']) as $questAnswer) {
            if ($questAnswer) {
                list($key, $value) = explode(':', $questAnswer);
                '' != $key && $questsAnswerList[$key][] = $value;
            }
        }

        $assign['quests_quest_list']  = $questsQuestList;
        $assign['quests_answer_list'] = $questsAnswerList;
        $assign['quests_info']        = $questsInfo;
        $assign['quests_answer_info'] = $questsAnswerInfo;
        $assign['edit_info'] = Model\QuestsAnswer::columnEmptyData();
        $assign['title'] = trans('quests.quests') . trans('common.answer');
        return view('admin.QuestsAnswer_add', $assign);
    }

    //统计问卷答案
    public function edit()
    {
        $questsId = request('quests_id');
        if (!$questsId) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::QuestsAnswer::index'));
        }

        //读取问题
        $questsInfo = Model\Quests::colWhere($questsId)->first()->toArray();
        //初始化问题
        $questsQuestList                            = is_array($questsInfo['ext_info']) ? $questsInfo['ext_info'] : [];
        foreach ($questsQuestList as $questId => $quest) {
            $answer     = [];
            switch ($quest['answer_type']) {
                case 'radio':
                    $answerName              = explode('|', $quest['answer']);
                    foreach ($answerName as $k => $name) {
                        $answer[$k]['name'] = $name;
                        $answer[$k]['count'] = Model\QuestsAnswer::whereQuestsAnswer($questsId,
                            '%|' . $questId . ':' . $k . "|%")->count();
                    }
                    break;
                case 'checkbox':
                    $answerName              = explode('|', $quest['answer']);
                    foreach ($answerName as $k => $name) {
                        $answer[$k]['name'] = $name;
                        $answer[$k]['count'] = Model\QuestsAnswer::whereQuestsAnswer($questsId,
                            '%|' . $questId . ':' . $k . "|%")->count();
                    }
                    break;
                case 'text':
                    $answer[0]['name']  = '';
                    $answer[0]['count'] = Model\QuestsAnswer::whereQuestsAnswer($questsId,
                        "%|" . $questId . ":%")->count();
                    break;
                case 'textarea':
                    $answer[0]['name']  = '';
                    $answer[0]['count'] = Model\QuestsAnswer::whereQuestsAnswer($questsId,
                        "%|" . $questId . ":%")->count();
                    break;
            }
            $questsQuestList[$questId]['answer']    = $answer;
            $questsQuestList[$questId]['max_count'] = Model\QuestsAnswer::whereQuestsAnswer($questsId,
                "%|" . $questId . ":%")->count();
        }
        $assign['quests_quest_list'] = $questsQuestList;

        $assign['title'] = trans('quests.quests') . trans('common.answer') . trans('common.statistics');
        return view('admin.QuestsAnswer_edit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::QuestsAnswer::index'));
        }

        $questsAnswerInfo = Model\QuestsAnswer::colWhere($id)->first()->toArray();
        $resultDel        = Model\QuestsAnswer::destroy($questsAnswerInfo['id']);
        if ($resultDel) {
            Model\Quests::where(['id' => $questsAnswerInfo['quests_id']])->decrement('current_portion');
            return $this->success(trans('common.del') . trans('common.answer') . trans('common.success'),
                route('Admin::QuestsAnswer::index'));
        } else {
            return $this->error(trans('common.del') . trans('common.answer') . trans('common.error'),
                route('Admin::QuestsAnswer::index'));
        }
    }
}
