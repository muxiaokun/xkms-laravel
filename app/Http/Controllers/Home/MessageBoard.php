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
        $messageBoardInfo = $MessageBoardModel->mFind($id);
        if (!$messageBoardInfo) {
            $this->error(L('messageboard') . L('dont') . L('exists'));
        }

        $this->assign('message_board_info', $messageBoardInfo);

        $MessageBoardLogModel   = D('MessageBoardLog');
        $where                  = array();
        $where['audit_id']      = array('gt', 0);
        $messageBoardLogList = $MessageBoardLogModel->order('add_time desc')->mSelect($where, true);
        foreach ($messageBoardLogList as &$messageBoardLog) {
            $messageBoardLog['reply_info'] = ($messageBoardLog['reply_info']) ? $messageBoardLog['reply_info'] : L('admin') . L('reply') . L('empty');
            $messageBoardLog['send_info']  = json_decode($messageBoardLog['send_info'], true);
        }
        $this->assign('message_board_log_list', $messageBoardLogList);
        $this->assign('message_board_log_list_count', $MessageBoardLogModel->mGetPageCount($where));

        $defTemplate = CONTROLLER_NAME . C('TMPL_FILE_DEPR') . ACTION_NAME;
        $template     = ($messageBoardInfo['template']) ? $defTemplate . '_' . $messageBoardInfo['template'] : $defTemplate;

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
            $messageBoardInfo = $MessageBoardModel->mFind($id);
            if (!$messageBoardInfo) {
                $this->error(L('id') . L('error'));
            }

            if (!$this->verifyCheck(I('verify')) && C('SYS_FRONTEND_VERIFY')) {
                $this->error(L('verify_code') . L('error'), U('index', array('id' => $id)));
            }
            $submitTime  = 300;
            $MessageBoard = D('MessageBoardLog');
            if ($MessageBoard->check_dont_submit($submitTime)) {
                $this->error($submitTime . L('second') . L('later') . L('again') . L('send'), U('index', array('id' => $id)));
            }
            $data       = $this->makeData();
            $resultAdd = $MessageBoard->mAdd($data);
            if ($resultAdd) {
                $this->success(L('send') . L('success'), U('index', array('id' => $id)));
                return;
            } else {
                $this->error(L('send') . L('error'), U('index', array('id' => $id)));
            }
        }
    }

    //建立数据
    private function makeData()
    {

        $id        = I('id');
        $sendInfo = I('send_info');
        foreach ($sendInfo as &$info) {
            if (is_array($info)) {
                $info = implode(',', $info);
            }

        }
        //检测数据
        $MessageBoardModel  = D('MessageBoard');
        $messageBoardInfo = $MessageBoardModel->mFind($id);
        $config             = $messageBoardInfo['config'];
        foreach ($sendInfo as $name => $value) {
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

        $sendInfo                                  = json_encode($sendInfo);
        $memberId                                  = session('frontend_info.id');
        $memberId                                  = ($memberId) ? $memberId : 0;
        $data                                       = array();
        (null !== $memberId) && $data['msg_id']    = $id;
        (null !== $memberId) && $data['send_id']   = $memberId;
        (null !== $sendInfo) && $data['send_info'] = $sendInfo;

        return $data;
    }
}
