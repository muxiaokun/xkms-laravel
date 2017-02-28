<?php
// 前台 调查问卷 答题

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;
use App\Model;
use Carbon\Carbon;

class Quests extends FrontendMember
{
    //列表
    public function index()
    {
        $questsList = Model\Quests::where(function ($query) {
            $currentTime = Carbon::now();
            $query->where('start_time', '<', $currentTime);
            $query->where('end_time', '>', $currentTime);
            $query->where(function ($query) {
                $query->whereColumn('current_portion', '<', 'max_portion');
                $query->orWhere('max_portion', '=', 0);
            });

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['quests_list'] = $questsList;

        $assign['title']       = trans('quests.quests');
        return view('home.Quests_index', $assign);
    }

    //添加
    public function add()
    {
        //初始化参数
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Home::Quests::index'));
        }

        $questsInfo = Model\Quests::colWhere($id)->first();
        //检测是否能够提交
        if (null === $questsInfo) {
            return $this->error(trans('quests.quests') . trans('common.dont') . trans('common.exists'),
                route('Home::Quests::index'));
        }
        $currentTime = Carbon::now();
        if ($questsInfo['start_time'] > $currentTime && $questsInfo['end_time'] < $currentTime) {
            return $this->error(trans('common.start') . trans('common.end') . trans('common.time') . trans('common.error'),
                route('Home::Quests::index'));
        }
        if (0 != $questsInfo['max_portion'] && $questsInfo['current_portion'] >= $questsInfo['max_portion']) {
            return $this->error(trans('common.gt') . trans('common.max') . trans('common.portion'),
                route('Home::Quests::index'));
        }
        $accessInfo = request('access_info');
        if (isset($questsInfo['access_info']) && $questsInfo['access_info'] != $accessInfo) {
            return $this->error(trans('common.access') . trans('common.info') . trans('common.error'),
                route('Home::Quests::index'));
        }
        //初始化问题
        $questsQuestList = is_array($questsInfo['ext_info']) ? $questsInfo['ext_info'] : [];
        foreach ($questsQuestList as $questId => $quest) {
            $questsQuestList[$questId]['answer'] = explode('|', $questsQuestList[$questId]['answer']);
        }
        //存入数据
        if (request()->isMethod('POST')) {
            $data = [];
            session('frontend_info.id') && $data['member_id'] = session('frontend_info.id');
            $data['quests_id'] = $id;
            $data['answer']    = '|';
            $questsAnswer      = request('quests');
            foreach ($questsQuestList as $questId => $quest) {
                if (isset($questsAnswer[$questId]) && $questsAnswer[$questId]) {
                    if ($quest['answer_type'] == 'checkbox') {
                        foreach ($questsAnswer[$questId] as $value) {
                            $data['answer'] .= $questId . ':' . $value . '|';
                        }
                    } else {
                        $data['answer'] .= $questId . ':' . $questsAnswer[$questId] . '|';
                    }
                }
            }
            $resultAdd = Model\QuestsAnswer::create($data);
            if ($resultAdd) {
                Model\Quests::where(['id' => $questsInfo['id']])->increment('current_portion');
                return $this->success(trans('common.answer') . trans('common.add') . trans('common.success'),
                    route('Home::Quests::index'));
            } else {
                return $this->error(trans('common.answer') . trans('common.add') . trans('common.error'),
                    route('Home::Quests::index'));
            }
            return;
        }

        $assign['quests_quest_list'] = $questsQuestList;
        $assign['quests_info']       = $questsInfo;
        $assign['title'] = trans('common.write') . trans('quests.quests');
        return view('home.Quests_add', $assign);
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
