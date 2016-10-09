<?php
// 后台 站内信

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Message extends Backend
{
    //列表
    public function index()
    {
        $MemberModel  = D('Member');
        $MessageModel = D('Message');
        $where        = [];
        //0为系统发送/接收
        $where['_complex']['_logic']     = 'OR';
        $where['_complex']['receive_id'] = $where['_complex']['send_id'] = 0;
        //建立where
        $whereValue = '';
        $whereValue = request('receive_id');
        $whereValue && $where['receive_id'] = [
            'in',
            $MemberModel->where(['member_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = mMktimeRange('send_time');
        $whereValue && $where['send_time'] = $whereValue;

        $messageList = $MessageModel->order('receive_time asc,send_time desc')->mSelect($where, true);
        foreach ($messageList as &$message) {
            $message['send_name']    = ($message['send_id']) ? $MemberModel->mFindColumn($message['send_id'],
                'member_name') : trans('system');
            $message['receive_name'] = ($message['receive_id']) ? $MemberModel->mFindColumn($message['receive_id'],
                'member_name') : trans('system');
        }
        $this->assign('message_list', $messageList);
        $this->assign('message_list_count', $MessageModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['receive_id'] = ['type' => 'input', 'name' => trans('receive') . trans('member')];
        $whereInfo['send_time']  = ['type' => 'time', 'name' => trans('send') . trans('time')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle        = [];
        $batchHandle['add'] = $this->_check_privilege('add');
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('message') . trans('management'));
        $this->display();
    }

    //发送信息
    public function add()
    {
        $receiveId    = request('receive_id');
        $MessageModel = D('Message');
        if (IS_POST) {
            $content = request('content');
            if (null == $content) {
                $this->error(trans('content') . trans('not') . trans('empty'), route('index'));
            }

            if (null == $receiveId) {
                $this->error(trans('receive') . trans('member') . trans('error'), route('index'));
            }

            $data      = [
                'send_id'    => session('frontend_info.id'),
                'receive_id' => $receiveId,
                'content'    => $content,
            ];
            $resultAdd = $MessageModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('send') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('send') . trans('error'), route('index'));
            }
        }

        if ($receiveId) {
            $MemberModel = D('Member');
            $this->assign('receive_info', $MemberModel->mFind($receiveId));
        }

        $this->assign('title', trans('send') . trans('message'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $MessageModel = D('Message');
        $resultDel    = $MessageModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('message') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('message') . trans('del') . trans('error'), route('index'));
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
                $MemberModel    = D('Member');
                $memberUserList = $MemberModel->mSelect($where);
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
            case 'read_message':
                $MessageModel = D('Message');
                $currentTime  = time();
                $where        = ['receive_id' => 0];
                $resultEdit   = $MessageModel->where($where)->mEdit($data['id'], ['receive_time' => $currentTime]);
                if ($resultEdit) {
                    $result['info'] = date(config('SYS_DATE_DETAIL'), $currentTime);
                } else {
                    $result['status'] = false;
                }
                break;
        }

        return $result;
    }
}
