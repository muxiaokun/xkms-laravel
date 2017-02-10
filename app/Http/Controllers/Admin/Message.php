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
        $where = [];
        //0为系统发送/接收
        //$where['_complex']['_logic']     = 'OR';
        //$where['_complex']['receive_id'] = $where['_complex']['send_id'] = 0;
        //建立where
        $whereValue = '';
        $whereValue = request('receive_id');
        $whereValue && $where[] = [
            'receive_id',
            'in',
            Model\Member::where(['member_name' => ['like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];
        $whereValue = mMktimeRange('send_time');
        $whereValue && $where[] = ['send_time', $whereValue];

        $messageList            = Model\Message::orderBy('receive_time', 'asc')->orderBy('send_time',
            'desc')->where($where)->paginate(config('system.sys_max_row'));
        $assign['message_list'] = $messageList;

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

        $assign['title'] = trans('message.message') . trans('common.management');
        return view('admin.Message_index', $assign);
    }

    //发送信息
    public function add()
    {
        $receiveId    = request('receive_id');
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
                'send_id'    => session('frontend_info.id'),
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
        $assign['title']     = trans('common.send') . trans('common.message');
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
            return $this->success(trans('common.message') . trans('common.del') . trans('common.success'),
                route('Admin::Message::index'));
        } else {
            return $this->error(trans('common.message') . trans('common.del') . trans('common.error'),
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
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                isset($data['keyword']) && $where['member_name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberUserList = Model\Member::where($where)->get();
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
            case 'read_message':
                $currentTime = Carbon::now();
                $resultEdit = Model\Message::colWhere(0, 'receive_id')->colWhere($data['id'])->first()->update($data);
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
