<?php
// 前台 调查问卷 答题

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;
use App\Model;

class Quests extends FrontendMember
{
    //列表
    public function index()
    {
        $currentTime                 = Carbon::now();
        $where                       = [
            'start_time' => ['lt', $currentTime],
            'end_time'   => ['gt', $currentTime],
            '(current_portion < max_portion OR max_portion = 0)',
        ];
        $questsList                  = Model\Quests::mSelect($where, true);
        $assign['quests_list']       = $questsList;
        $assign['quests_list_count'] = Model\Quests::mGetPageCount($where);

        $assign['title'] = trans('common.quests');
        return view('home.', $assign);
    }

    //添加
    public function add()
    {
        //初始化参数
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Quests/index'));
        }

        $questsInfo = Model\Quests::mFind($id);
        //检测是否能够提交
        $currentTime = Carbon::now();
        if ($questsInfo['start_time'] < $currentTime && $questsInfo['end_time'] < $currentTime) {
            return $this->error(trans('common.start') . trans('common.end') . trans('common.time') . trans('common.error'),
                route('Quests/index'));
        }
        if (0 != $questsInfo['max_portion'] && $questsInfo['current_portion'] >= $questsInfo['max_portion']) {
            return $this->error(trans('common.gt') . trans('common.max') . trans('common.portion'),
                route('Quests/index'));
        }
        $accessInfo = request('access_info');
        if (isset($questsInfo['access_info']) && $questsInfo['access_info'] != $accessInfo) {
            return $this->error(trans('common.access') . trans('common.pass') . trans('common.error'),
                route('Quests/index'));
        }
        //初始化问题
        $questsQuestList = json_decode($questsInfo['ext_info'], true);
        foreach ($questsQuestList as $questId => $quest) {
            $questsQuestList[$questId]['answer'] = explode('|', $questsQuestList[$questId]['answer']);
        }
        //存入数据
        if (request()->isMethod('POST')) {
            $data = [];
            session('frontend.id') && $data['member_id'] = session('frontend.id');
            $data['quests_id'] = $id;
            $data['answer']    = '|';
            $questsAnswer      = request('quests');
            foreach ($questsQuestList as $questId => $quest) {
                if ($quest['answer_type'] == 'checkbox') {
                    foreach ($questsAnswer[$questId] as $value) {
                        $data['answer'] .= $questId . ':' . $value . '|';
                    }
                } else {
                    $data['answer'] .= $questId . ':' . $questsAnswer[$questId] . '|';
                }
            }
            $resultAdd = Model\QuestsAnswer::mAdd($data);
            if ($resultAdd) {
                Model\Quests::where(['id' => $questsInfo['id']])->setInc('current_portion');
                return $this->success(trans('common.answer') . trans('common.add') . trans('common.success'),
                    route('Quests/index'));
            } else {
                return $this->error(trans('common.answer') . trans('common.add') . trans('common.error'),
                    route('index'));
            }
            return;
        }

        $assign['quests_quest_list'] = $questsQuestList;
        $assign['quests_info']       = $questsInfo;
        $assign['title']             = trans('common.write') . trans('common.quests');
        return view('home.', $assign);
    }
}
/*
array (size=2)
0 =>
array (size=5)
'question' => string 'asdf' (length=4)
'explains' => string 'asdf' (length=4)
'answer' => string 'asdf' (length=4)
'required' => boolean true
'answer_type' => string 'radio' (length=5)
1 =>
array (size=5)
'question' => string 'asdfasdf' (length=8)
'explains' => string 'asdfasdf' (length=8)
'answer' => string 'asdfas' (length=6)
'required' => boolean true
'answer_type' => string 'radio' (length=5)
 *  */
