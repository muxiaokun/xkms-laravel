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
// 前台 调查问卷 答题

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Quests extends FrontendMember
{
    //列表
    public function index()
    {
        $QuestsModel  = D('Quests');
        $current_time = time();
        $where        = array(
            'start_time' => array('lt', $current_time),
            'end_time'   => array('gt', $current_time),
            '(current_portion < max_portion OR max_portion = 0)',
        );
        $quests_list = $QuestsModel->m_select($where, true);
        $this->assign('quests_list', $quests_list);
        $this->assign('quests_list_count', $QuestsModel->get_page_count($where));

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
        $quests_info = $QuestsModel->m_find($id);
        //检测是否能够提交
        $current_time = time();
        if ($quests_info['start_time'] < $current_time && $quests_info['end_time'] < $current_time) {
            $this->error(L('start') . L('end') . L('time') . L('error'), U('Quests/index'));
        }
        if (0 != $quests_info['max_portion'] && $quests_info['current_portion'] >= $quests_info['max_portion']) {
            $this->error(L('gt') . L('max') . L('portion'), U('Quests/index'));
        }
        $access_info = I('access_info');
        if (isset($quests_info['access_info']) && $quests_info['access_info'] != $access_info) {
            $this->error(L('access') . L('pass') . L('error'), U('Quests/index'));
        }
        //初始化问题
        $quests_quest_list = json_decode($quests_info['ext_info'], true);
        foreach ($quests_quest_list as $quest_id => $quest) {
            $quests_quest_list[$quest_id]['answer'] = explode('|', $quests_quest_list[$quest_id]['answer']);
        }
        //存入数据
        if (IS_POST) {
            $data                                        = array();
            session('frontend.id') && $data['member_id'] = session('frontend.id');
            $data['quests_id']                           = $id;
            $data['answer']                              = '|';
            $quests_answer                               = I('quests');
            foreach ($quests_quest_list as $quest_id => $quest) {
                if ($quest['answer_type'] == 'checkbox') {
                    foreach ($quests_answer[$quest_id] as $value) {
                        $data['answer'] .= $quest_id . ':' . $value . '|';
                    }
                } else {
                    $data['answer'] .= $quest_id . ':' . $quests_answer[$quest_id] . '|';
                }
            }
            $QuestsAnswerModel = D('QuestsAnswer');
            $result_add        = $QuestsAnswerModel->m_add($data);
            if ($result_add) {
                $QuestsModel->where(array('id' => $quests_info['id']))->setInc('current_portion');
                $this->success(L('answer') . L('add') . L('success'), U('Quests/index'));
            } else {
                $this->error(L('answer') . L('add') . L('error'), U('index'));
            }
            return;
        }

        $this->assign('quests_quest_list', $quests_quest_list);
        $this->assign('quests_info', $quests_info);
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
