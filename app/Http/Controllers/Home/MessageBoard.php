<?php
// 前台 留言板

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;

class MessageBoard extends Frontend
{
    public function index()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'));
        }

        $messageBoardInfo = Model\MessageBoard::colWhere($id)->first()->toArray();
        if (!$messageBoardInfo) {
            return $this->error(trans('common.messageboard') . trans('common.dont') . trans('common.exists'));
        }

        $assign['message_board_info'] = $messageBoardInfo;

        $where               = [];
        $where['audit_id']   = ['gt', 0];
        $messageBoardLogList              = Model\MessageBoardLog::orderBy('add_time',
            'desc')->where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($messageBoardLogList as &$messageBoardLog) {
            $messageBoardLog['reply_info'] = ($messageBoardLog['reply_info']) ? $messageBoardLog['reply_info'] : trans('common.admin') . trans('common.reply') . trans('common.empty');
            $messageBoardLog['send_info']  = json_decode($messageBoardLog['send_info'], true);
        }
        $assign['message_board_log_list'] = $messageBoardLogList;

        $defTemplate = CONTROLLER_NAME . config('TMPL_FILE_DEPR') . ACTION_NAME;
        $template    = ($messageBoardInfo['template']) ? $defTemplate . '_' . $messageBoardInfo['template'] : $defTemplate;

        $assign['title'] = trans('common.messageboard');
        return view($template, $assign);
    }

    //添加
    public function add()
    {
        if (request()->isMethod('POST')) {
            $id = request('id');
            if (!$id) {
                return $this->error(trans('common.id') . trans('common.error'));
            }

            $messageBoardInfo = Model\MessageBoard::colWhere($id)->first()->toArray();
            if (!$messageBoardInfo) {
                return $this->error(trans('common.id') . trans('common.error'));
            }

            if (!$this->verifyCheck(request('verify')) && config('system.sys_frontend_verify')) {
                return $this->error(trans('common.verify_code') . trans('common.error'),
                    route('Home::MessageBoard::index', ['id' => $id]));
            }
            $submitTime = 300;
            if (Model\MessageBoard::check_dont_submit($submitTime)) {
                return $this->error($submitTime . trans('common.second') . trans('common.later') . trans('common.again') . trans('common.send'),
                    route('Home::MessageBoard::index', ['id' => $id]));
            }
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\MessageBoard::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.send') . trans('common.success'),
                    route('Home::MessageBoard::index', ['id' => $id]));
            } else {
                return $this->error(trans('common.send') . trans('common.error'),
                    route('Home::MessageBoard::index', ['id' => $id]));
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
        $messageBoardInfo = Model\MessageBoard::colWhere($id)->first()->toArray();
        $config           = $messageBoardInfo['config'];
        foreach ($sendInfo as $name => $value) {
            //合法
            if (!is_array($config[$name])) {
                return $this->error(trans('common.submit') . trans('common.error'),
                    route('Home::MessageBoard::index', ['id' => $id]));
            }

            //必选
            if (isset($config[$name]['msg_required']) && '' == $value) {
                return $this->error($name . trans('common.required'),
                    route('Home::MessageBoard::index', ['id' => $id]));
            }

            //长度
            if (0 < $config[$name]['msg_length'] && $config[$name]['msg_length'] < strlen($value)) {
                return $this->error($name . trans('common.max') . trans('common.length') . $config[$name]['msg_length'],
                    route('Home::MessageBoard::index', ['id' => $id]));
            }
        }

        $memberId = session('frontend_info.id');
        $memberId = ($memberId) ? $memberId : 0;
        $data     = [];
        (null !== $memberId) && $data['msg_id'] = $id;
        (null !== $memberId) && $data['send_id'] = $memberId;
        (null !== $sendInfo) && $data['send_info'] = $sendInfo;

        return $data;
    }
}
