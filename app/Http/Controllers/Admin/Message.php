<?php
// 后台 站内信

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Message extends Backend
{
    //列表
    public function index()
    {
        $where        = [];
        //0为系统发送/接收
        $where['_complex']['_logic']     = 'OR';
        $where['_complex']['receive_id'] = $where['_complex']['send_id'] = 0;
        //建立where
        $whereValue = '';
        $whereValue = request('receive_id');
        $whereValue && $where['receive_id'] = [
            'in',
            Model\Member::where(['member_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = mMktimeRange('send_time');
        $whereValue && $where['send_time'] = $whereValue;

        $messageList = Model\Message::order('receive_time asc,send_time desc')->mSelect($where, true);
        foreach ($messageList as &$message) {
            $message['send_name']    = ($message['send_id']) ? Model\Member::mFindColumn($message['send_id'],
                'member_name') : trans('common.system');
            $message['receive_name'] = ($message['receive_id']) ? Model\Member::mFindColumn($message['receive_id'],
                'member_name') : trans('common.system');
        }
        $assign['message_list']       = $messageList;
        $assign['message_list_count'] = Model\Message::mGetPageCount($where);

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['receive_id'] = ['type' => 'input', 'name' => trans('common.receive') . trans('common.member')];
        $whereInfo['send_time']  = ['type' => 'time', 'name' => trans('common.send') . trans('common.time')];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.message') . trans('common.management');
        return view('admin.', $assign);
    }

    //发送信息
    public function add()
    {
        $receiveId    = request('receive_id');
        if (IS_POST) {
            $content = request('content');
            if (null == $content) {
                $this->error(trans('common.content') . trans('common.not') . trans('common.empty'), route('index'));
            }

            if (null == $receiveId) {
                $this->error(trans('common.receive') . trans('common.member') . trans('common.error'), route('index'));
            }

            $data      = [
                'send_id'    => session('frontend_info.id'),
                'receive_id' => $receiveId,
                'content'    => $content,
            ];
            $resultAdd = Model\Message::mAdd($data);
            if ($resultAdd) {
                $this->success(trans('common.send') . trans('common.success'), route('index'));
                return;
            } else {
                $this->error(trans('common.send') . trans('common.error'), route('index'));
            }
        }

        if ($receiveId) {
            $assign['receive_info'] = Model\Member::mFind($receiveId);
        }

        $assign['title'] = trans('common.send') . trans('common.message');
        return view('admin.', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\Message::mDel($id);
        if ($resultDel) {
            $this->success(trans('common.message') . trans('common.del') . trans('common.success'), route('index'));
            return;
        } else {
            $this->error(trans('common.message') . trans('common.del') . trans('common.error'), route('index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'receive_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                isset($data['keyword']) && $where['member_name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberUserList = Model\Member::mSelect($where);
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
            case 'read_message':
                $currentTime = time();
                $where       = ['receive_id' => 0];
                $resultEdit  = Model\Message::where($where)->mEdit($data['id'], ['receive_time' => $currentTime]);
                if ($resultEdit) {
                    $result['info'] = date(config('system.sys_date_detail'), $currentTime);
                } else {
                    $result['status'] = false;
                }
                break;
        }

        return $result;
    }
}
