<?php
// 后台 站内信

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Carbon\Carbon;

class Message extends Backend
{
    //列表
    public function index()
    {
        $messageList = Model\Message::where(function ($query) {
            $query->where(function ($query) {
                $query->orWhere('send_id', '=', 0);
                $query->orWhere('receive_id', '=', 0);
            });

            $member_name = request('member_name');
            if ($member_name) {
                $memberIds = Model\Member::where('member_name', 'like',
                    '%' . $member_name . '%')->select(['id'])->pluck('id');
                $query->whereIn('receive_id', $memberIds);
            }

            $created_at = mMktimeRange('created_at');
            if ($created_at) {
                $query->timeWhere('created_at', $created_at);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());

        foreach ($messageList as &$message) {
            if ($message['send_id']) {
                $memberInfo           = Model\Member::colWhere($message['send_id'])->first();
                $message['send_name'] = (null === $memberInfo) ? $message['send_id'] : $memberInfo['member_name'];
            } else {
                $message['send_name'] = trans('common.system') . trans('common.send');
            }
            if ($message['receive_id']) {
                $memberInfo              = Model\Member::colWhere($message['receive_id'])->first();
                $message['receive_name'] = (null === $memberInfo) ? $message['receive_id'] : $memberInfo['member_name'];
            } else {
                $message['receive_name'] = trans('common.system') . trans('common.receive');
            }
        }
        $assign['message_list'] = $messageList;

        //初始化where_info
        $whereInfo                = [];
        $whereInfo['member_name'] = ['type' => 'input', 'name' => trans('common.receive') . trans('common.member')];
        $whereInfo['created_at']  = ['type' => 'time', 'name' => trans('common.send') . trans('common.time')];
        $assign['where_info']     = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('message.message') . trans('common.management');
        return view('admin.Message_index', $assign);
    }

    //发送信息
    public function add()
    {
        $receiveId = request('receive_id');
        if (request()->isMethod('POST')) {
            $content = request('content');
            if (null == $content) {
                return $this->error(trans('common.content') . trans('common.not') . trans('common.empty'),
                    route('Admin::Message::index'));
            }

            if (null == $receiveId) {
                return $this->error(trans('common.receive') . trans('common.member') . trans('common.error'),
                    route('Admin::Message::index'));
            }

            $data      = [
                'send_id'    => 0,//0为系统发送
                'receive_id' => $receiveId,
                'content'    => $content,
            ];
            $resultAdd = Model\Message::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.send') . trans('common.success'), route('Admin::Message::index'));
            } else {
                return $this->error(trans('common.send') . trans('common.error'), route('Admin::Message::index'));
            }
        }

        if ($receiveId) {
            $assign['receive_info'] = Model\Member::colWhere($receiveId)->first()->toArray();
        }

        $assign['edit_info'] = Model\Message::columnEmptyData();
        $assign['title'] = trans('common.send') . trans('message.message');
        return view('admin.Message_add', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Message::index'));
        }

        $resultDel = Model\Message::destroy($id);
        if ($resultDel) {
            return $this->success(trans('message.message') . trans('common.del') . trans('common.success'),
                route('Admin::Message::index'));
        } else {
            return $this->error(trans('message.message') . trans('common.del') . trans('common.error'),
                route('Admin::Message::index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'receive_id':
                Model\Member::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('member_name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['member_name']];
                });
                break;
            case 'read_message':
                $currentTime = Carbon::now()->toDateTimeString();
                $resultEdit  = Model\Message::where([['id', $data['id']], ['receive_id', 0]])->first()->update([
                    'updated_at' => $currentTime,
                ]);
                if ($resultEdit) {
                    $result['info'] = $currentTime;
                } else {
                    $result['status'] = false;
                }
                break;
        }

        return $result;
    }
}
