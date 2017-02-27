<?php
// 前台 留言板

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;
use Carbon\Carbon;

class MessageBoard extends Frontend
{
    public function index()
    {
        $id = request('id', 1);

        $messageBoardInfo = Model\MessageBoard::colWhere($id)->first();
        if (null === $messageBoardInfo) {
            return $this->error(trans('common.messageboard') . trans('common.dont') . trans('common.exists'),
                route('Home::Member::index'));
        }

        $assign['message_board_info'] = $messageBoardInfo;

        $messageBoardLogList = Model\MessageBoardLog::where(function ($query) {
            $query->where('audit_id', '>', 0);

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['message_board_log_list'] = $messageBoardLogList;

        $defTemplate = 'home.MessageBoard_index';
        $template    = ($messageBoardInfo['template']) ? $defTemplate . '_' . $messageBoardInfo['template'] : $defTemplate;

        $assign['title'] = trans('common.messageboard');

        return view($template, $assign);
    }

    //添加
    public function add()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'));
        }

        $messageBoardInfo = Model\MessageBoard::colWhere($id)->first();
        if (null === $messageBoardInfo) {
            return $this->error(trans('common.messageboard') . trans('common.dont') . trans('common.exists'),
                route('Home::Member::index'));
        }

        if (!$this->verifyCheck(request('verify')) && config('system.sys_frontend_verify')) {
            return $this->error(trans('common.verify_code') . trans('common.error'),
                route('Home::MessageBoard::index', ['id' => $id]));
        }

        $submitTime          = 300;
        $messageBoardLogInfo = Model\MessageBoardLog::where(function ($query) use ($submitTime) {
            $query->where('add_ip', '=', request()->ip());
            $query->where('created_at', '>', Carbon::now()->subSecond($submitTime));
        })->first();
        if (null !== $messageBoardLogInfo) {
            return $this->error($submitTime . trans('common.second') . trans('common.later') . trans('common.again') . trans('common.send'),
                route('Home::MessageBoard::index', ['id' => $id]));
        }

        $data = $this->makeData();
        if (!is_array($data)) {
            return $data;
        }

        $resultAdd = Model\MessageBoardLog::create($data);
        if ($resultAdd) {
            return $this->success(trans('common.send') . trans('common.success'),
                route('Home::MessageBoard::index', ['id' => $id]));
        } else {
            return $this->error(trans('common.send') . trans('common.error'),
                route('Home::MessageBoard::index', ['id' => $id]));
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

        $memberId          = session('frontend_info.id', 0);
        $data     = [];
        $data['mb_id']     = $id;
        $data['send_id']   = $memberId;
        $data['send_info'] = $sendInfo;
        $data['audit_id']  = 0;
        $data['add_ip']    = request()->ip();

        return $data;
    }
}
