<?php
// 前台 调查问卷 答题

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Quests extends FrontendMember
{
    //列表
    public function index()
    {
        $QuestsModel  = D('Quests');
        $currentTime = time();
        $where        = array(
            'start_time' => array('lt', $currentTime),
            'end_time'   => array('gt', $currentTime),
            '(current_portion < max_portion OR max_portion = 0)',
        );
        $questsList = $QuestsModel->mSelect($where, true);
        $this->assign('quests_list', $questsList);
        $this->assign('quests_list_count', $QuestsModel->mGetPageCount($where));

        $this->assign('title', L('quests'));
        $this->display();
    }

    //添加
    public function add()
    {
        //初始化参数
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('Quests/index'));
        }

        $QuestsModel = D('Quests');
        $questsInfo = $QuestsModel->mFind($id);
        //检测是否能够提交
        $currentTime = time();
        if ($questsInfo['start_time'] < $currentTime && $questsInfo['end_time'] < $currentTime) {
            $this->error(L('start') . L('end') . L('time') . L('error'), U('Quests/index'));
        }
        if (0 != $questsInfo['max_portion'] && $questsInfo['current_portion'] >= $questsInfo['max_portion']) {
            $this->error(L('gt') . L('max') . L('portion'), U('Quests/index'));
        }
        $accessInfo = I('access_info');
        if (isset($questsInfo['access_info']) && $questsInfo['access_info'] != $accessInfo) {
            $this->error(L('access') . L('pass') . L('error'), U('Quests/index'));
        }
        //初始化问题
        $questsQuestList = json_decode($questsInfo['ext_info'], true);
        foreach ($questsQuestList as $questId => $quest) {
            $questsQuestList[$questId]['answer'] = explode('|', $questsQuestList[$questId]['answer']);
        }
        //存入数据
        if (IS_POST) {
            $data                                        = array();
            session('frontend.id') && $data['member_id'] = session('frontend.id');
            $data['quests_id']                           = $id;
            $data['answer']                              = '|';
            $questsAnswer                               = I('quests');
            foreach ($questsQuestList as $questId => $quest) {
                if ($quest['answer_type'] == 'checkbox') {
                    foreach ($questsAnswer[$questId] as $value) {
                        $data['answer'] .= $questId . ':' . $value . '|';
                    }
                } else {
                    $data['answer'] .= $questId . ':' . $questsAnswer[$questId] . '|';
                }
            }
            $QuestsAnswerModel = D('QuestsAnswer');
            $resultAdd        = $QuestsAnswerModel->mAdd($data);
            if ($resultAdd) {
                $QuestsModel->where(array('id' => $questsInfo['id']))->setInc('current_portion');
                $this->success(L('answer') . L('add') . L('success'), U('Quests/index'));
            } else {
                $this->error(L('answer') . L('add') . L('error'), U('index'));
            }
            return;
        }

        $this->assign('quests_quest_list', $questsQuestList);
        $this->assign('quests_info', $questsInfo);
        $this->assign('title', L('write') . L('quests'));
        $this->display();
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
