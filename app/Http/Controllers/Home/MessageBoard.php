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
// 前台 留言板

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class MessageBoard extends Frontend
{
    public function index()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'));
        }

        $MessageBoardModel  = D('MessageBoard');
        $message_board_info = $MessageBoardModel->mFind($id);
        if (!$message_board_info) {
            $this->error(L('messageboard') . L('dont') . L('exists'));
        }

        $this->assign('message_board_info', $message_board_info);

        $MessageBoardLogModel   = D('MessageBoardLog');
        $where                  = array();
        $where['audit_id']      = array('gt', 0);
        $message_board_log_list = $MessageBoardLogModel->order('add_time desc')->mSelect($where, true);
        foreach ($message_board_log_list as &$message_board_log) {
            $message_board_log['reply_info'] = ($message_board_log['reply_info']) ? $message_board_log['reply_info'] : L('admin') . L('reply') . L('empty');
            $message_board_log['send_info']  = json_decode($message_board_log['send_info'], true);
        }
        $this->assign('message_board_log_list', $message_board_log_list);
        $this->assign('message_board_log_list_count', $MessageBoardLogModel->getPageCount($where));

        $def_template = CONTROLLER_NAME . C('TMPL_FILE_DEPR') . ACTION_NAME;
        $template     = ($message_board_info['template']) ? $def_template . '_' . $message_board_info['template'] : $def_template;

        $this->assign('title', L('messageboard'));
        $this->display($template);
    }

    //添加
    public function add()
    {
        if (IS_POST) {
            $id = I('id');
            if (!$id) {
                $this->error(L('id') . L('error'));
            }

            $MessageBoardModel  = D('MessageBoard');
            $message_board_info = $MessageBoardModel->mFind($id);
            if (!$message_board_info) {
                $this->error(L('id') . L('error'));
            }

            if (!$this->_verify_check(I('verify')) && C('SYS_FRONTEND_VERIFY')) {
                $this->error(L('verify_code') . L('error'), U('index', array('id' => $id)));
            }
            $submit_time  = 300;
            $MessageBoard = D('MessageBoardLog');
            if ($MessageBoard->check_dont_submit($submit_time)) {
                $this->error($submit_time . L('second') . L('later') . L('again') . L('send'), U('index', array('id' => $id)));
            }
            $data       = $this->_make_data();
            $result_add = $MessageBoard->mAdd($data);
            if ($result_add) {
                $this->success(L('send') . L('success'), U('index', array('id' => $id)));
                return;
            } else {
                $this->error(L('send') . L('error'), U('index', array('id' => $id)));
            }
        }
    }

    //建立数据
    private function _make_data()
    {

        $id        = I('id');
        $send_info = I('send_info');
        foreach ($send_info as &$info) {
            if (is_array($info)) {
                $info = implode(',', $info);
            }

        }
        //检测数据
        $MessageBoardModel  = D('MessageBoard');
        $message_board_info = $MessageBoardModel->mFind($id);
        $config             = $message_board_info['config'];
        foreach ($send_info as $name => $value) {
            //合法
            if (!is_array($config[$name])) {
                $this->error(L('submit') . L('error'), U('index', array('id' => $id)));
            }

            //必选
            if (isset($config[$name]['msg_required']) && '' == $value) {
                $this->error($name . L('required'), U('index', array('id' => $id)));
            }

            //长度
            if (0 < $config[$name]['msg_length'] && $config[$name]['msg_length'] < strlen($value)) {
                $this->error($name . L('max') . L('length') . $config[$name]['msg_length'], U('index', array('id' => $id)));
            }
        }

        $send_info                                  = json_encode($send_info);
        $member_id                                  = session('frontend_info.id');
        $member_id                                  = ($member_id) ? $member_id : 0;
        $data                                       = array();
        (null !== $member_id) && $data['msg_id']    = $id;
        (null !== $member_id) && $data['send_id']   = $member_id;
        (null !== $send_info) && $data['send_info'] = $send_info;

        return $data;
    }
}
