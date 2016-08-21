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
        $v_value                        = '';
        $v_value                        = I('quests_id');
        $v_value && $where['quests_id'] = $v_value;
        $v_value                        = I('quests_title');
        $v_value && $where['quests_id'] = array(
            'in',
            $QuestsModel->where(array('title' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );
        $v_value                        = I('member_id');
        $v_value && $where['member_id'] = array(
            'in',
            $MemberModel->where(array('member_name' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );

        $QuestsAnswerModel  = D('QuestsAnswer');
        $quests_answer_list = $QuestsAnswerModel->mSelect($where, true);
        foreach ($quests_answer_list as &$quests_answer) {
            $member_name                  = $MemberModel->mFindColumn($group_id, 'name');
            $quests_answer['member_name'] = ($member_name) ? $member_name : L('anonymous');
        }
        $this->assign('quests_answer_list', $quests_answer_list);
        $this->assign('quests_answer_list_count', $QuestsAnswerModel->getPageCount($where));
        //初始化where_info
        $where_info                 = array();
        $where_info['quests_title'] = array('type' => 'input', 'name' => L('quests') . L('name'));
        $where_info['member_id']    = array('type' => 'input', 'name' => L('member') . L('name'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle        = array();
        $batch_handle['add'] = $this->_check_privilege('add');
        $batch_handle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

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
        $quests_answer_info = $QuestsAnswerModel->mFind($id);
        $quests_info        = $QuestsModel->mFind($quests_answer_info['quests_id']);

        //初始化问题
        $quests_quest_list = json_decode($quests_info['ext_info'], true);
        foreach ($quests_quest_list as $quest_id => $quest) {
            $quests_quest_list[$quest_id]['answer'] = explode('|', $quests_quest_list[$quest_id]['answer']);
        }
        $quests_answer_list = array();
        foreach (explode('|', $quests_answer_info['answer']) as $quest_answer) {
            list($key, $value)                        = explode(':', $quest_answer);
            '' != $key && $quests_answer_list[$key][] = $value;
        }

        $this->assign('quests_quest_list', $quests_quest_list);
        $this->assign('quests_answer_list', $quests_answer_list);
        $this->assign('quests_info', $quests_info);
        $this->assign('quests_answer_info', $quests_answer_info);
        $this->assign('title', L('quests') . L('answer'));
        $this->display();
    }

    //统计问卷答案
    public function edit()
    {
        $quests_id = I('quests_id');
        if (!$quests_id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        //读取问题
        $QuestsModel = D('Quests');
        $quests_info = $QuestsModel->mFind($quests_id);
        //初始化问题
        $quests_quest_list = json_decode($quests_info['ext_info'], true);
        $QuestsAnswerModel = D('QuestsAnswer');
        foreach ($quests_quest_list as $quest_id => $quest) {
            $answer_name = explode('|', $quests_quest_list[$quest_id]['answer']);
            $answer      = array();
            switch ($quest['answer_type']) {
                case 'radio':
                    foreach ($answer_name as $k => $name) {
                        $answer[$k]['name']  = $name;
                        $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($quests_id, '%|' . $quest_id . ':' . $k . "|%");
                    }
                    break;
                case 'checkbox':
                    foreach ($answer_name as $k => $name) {
                        $answer[$k]['name']  = $name;
                        $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($quests_id, '%|' . $quest_id . ':' . $k . "|%");
                    }
                    break;
                case 'text':
                    $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($quests_id, "%|" . $quest_id . ":%");
                    break;
                case 'textarea':
                    $answer[$k]['count'] = $QuestsAnswerModel->count_quests_answer($quests_id, "%|" . $quest_id . ":%");
                    break;
            }
            $quests_quest_list[$quest_id]['answer']    = $answer;
            $quests_quest_list[$quest_id]['max_count'] = $QuestsAnswerModel->count_quests_answer($quests_id);
        }
        $this->assign('quests_quest_list', $quests_quest_list);

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
        $quests_answer_info = $QuestsAnswerModel->mFind($id);
        $result_del         = $QuestsAnswerModel->mDel($quests_answer_info['id']);
        if ($result_del) {
            $QuestsModel = D('Quests');
            $QuestsModel->where(array('id' => $quests_answer_info['quests_id']))->setDec('current_portion');
            $this->success(L('del') . L('answer') . L('success'), U('index'));
        } else {
            $this->error(L('del') . L('answer') . L('error'), U('index'));
        }
    }
}
