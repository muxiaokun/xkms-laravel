<?php
// 前台 留言板

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class MessageBoard extends Frontend
{
    public function index()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'));
        }

        $MessageBoardModel = D('MessageBoard');
        $messageBoardInfo  = $MessageBoardModel->mFind($id);
        if (!$messageBoardInfo) {
            $this->error(trans('messageboard') . trans('dont') . trans('exists'));
        }

        $this->assign('message_board_info', $messageBoardInfo);

        $MessageBoardLogModel = D('MessageBoardLog');
        $where                = [];
        $where['audit_id']    = ['gt', 0];
        $messageBoardLogList  = $MessageBoardLogModel->order('add_time desc')->mSelect($where, true);
        foreach ($messageBoardLogList as &$messageBoardLog) {
            $messageBoardLog['reply_info'] = ($messageBoardLog['reply_info']) ? $messageBoardLog['reply_info'] : trans('admin') . trans('reply') . trans('empty');
            $messageBoardLog['send_info']  = json_decode($messageBoardLog['send_info'], true);
        }
        $this->assign('message_board_log_list', $messageBoardLogList);
        $this->assign('message_board_log_list_count', $MessageBoardLogModel->mGetPageCount($where));

        $defTemplate = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . ACTION_NAME;
        $template    = ($messageBoardInfo['template']) ? $defTemplate . '_' . $messageBoardInfo['template'] : $defTemplate;

        $this->assign('title', trans('messageboard'));
        $this->display($template);
    }

    //添加
    public function add()
    {
        if (IS_POST) {
            $id = request('id');
            if (!$id) {
                $this->error(trans('id') . trans('error'));
            }

            $MessageBoardModel = D('MessageBoard');
            $messageBoardInfo  = $MessageBoardModel->mFind($id);
            if (!$messageBoardInfo) {
                $this->error(trans('id') . trans('error'));
            }

            if (!$this->verifyCheck(request('verify')) && config('system.sys_frontend_verify')) {
                $this->error(trans('verify_code') . trans('error'), route('index', ['id' => $id]));
            }
            $submitTime   = 300;
            $MessageBoard = D('MessageBoardLog');
            if ($MessageBoard->check_dont_submit($submitTime)) {
                $this->error($submitTime . trans('second') . trans('later') . trans('again') . trans('send'),
                    route('index', ['id' => $id]));
            }
            $data      = $this->makeData();
            $resultAdd = $MessageBoard->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('send') . trans('success'), route('index', ['id' => $id]));
                return;
            } else {
                $this->error(trans('send') . trans('error'), route('index', ['id' => $id]));
            }
        }
    }

    //建立数据
    private function makeData()
    {

        $id       = request('id');
        $sendInfo = request('send_info');
        foreach ($sendInfo as &$info) {
            if (is_array($info)) {
                $info = implode(',', $info);
            }

        }
        //检测数据
        $MessageBoardModel = D('MessageBoard');
        $messageBoardInfo  = $MessageBoardModel->mFind($id);
        $config            = $messageBoardInfo['config'];
        foreach ($sendInfo as $name => $value) {
            //合法
            if (!is_array($config[$name])) {
                $this->error(trans('submit') . trans('error'), route('index', ['id' => $id]));
            }

            //必选
            if (isset($config[$name]['msg_required']) && '' == $value) {
                $this->error($name . trans('required'), route('index', ['id' => $id]));
            }

            //长度
            if (0 < $config[$name]['msg_length'] && $config[$name]['msg_length'] < strlen($value)) {
                $this->error($name . trans('max') . trans('length') . $config[$name]['msg_length'],
                    route('index', ['id' => $id]));
            }
        }

        $sendInfo = json_encode($sendInfo);
        $memberId = session('frontend_info.id');
        $memberId = ($memberId) ? $memberId : 0;
        $data     = [];
        (null !== $memberId) && $data['msg_id'] = $id;
        (null !== $memberId) && $data['send_id'] = $memberId;
        (null !== $sendInfo) && $data['send_info'] = $sendInfo;

        return $data;
    }
}
