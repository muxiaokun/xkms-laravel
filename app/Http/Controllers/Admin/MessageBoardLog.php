<?php
// 后台 留言板

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class MessageBoardLog extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('msg_id');
        $whereValue && $where['msg_id'] = $whereValue;
        $whereValue = request('audit_id');
        $whereValue && $where[] = [
            'audit_id',
            'in',
            Model\Admin::where(['admin_name' => ['like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where[] = ['add_time', $whereValue];

        $messageBoardLogList              = Model\MessageBoardLog::orderBy('add_time',
            'desc')->where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['message_board_log_list'] = $messageBoardLogList;

        //初始化where_info
        $whereInfo             = [];
        $whereInfo['audit_id'] = [
            'type' => 'input',
            'name' => trans('common.audit') . trans('common.admin') . trans('common.name'),
        ];
        $whereInfo['add_time'] = ['type' => 'time', 'name' => trans('common.send') . trans('common.time')];
        $assign['where_info']  = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.messageboard') . trans('common.management');
        return view('admin.MessageBoardLog_index', $assign);
    }

    //审核回复
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::MessageBoardLog::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = [
                'audit_id'   => session('backend_info.id'),
                'reply_info' => request('reply_info'),
            ];

            $resultEdit = false;
            Model\MessageBoardLog::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('common.audit') . trans('common.success'),
                    route('Admin::MessageBoardLog::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::MessageBoardLog::index') : route('Admin::MessageBoardLog::edit',
                    ['id' => $id]);
                return $this->error(trans('common.audit') . trans('common.error'), $errorGoLink);
            }
        }


        $editInfo                = Model\MessageBoardLog::colWhere($id)->first()->toArray();
        $memberName              = Model\Member::colWhere($editInfo['send_id'])->first()['member_name'];
        $editInfo['member_name'] = ($memberName) ? $memberName : trans('common.empty');
        $editInfo['send_info']   = json_decode($editInfo['send_info'], true);
        $assign['edit_info']     = $editInfo;

        $assign['title'] = trans('common.messageboard') . trans('common.audit');
        return view('admin.MessageBoardLog_edit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::MessageBoardLog::index'));
        }

        $resultDel = Model\MessageBoardLog::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.messageboard') . trans('common.log') . trans('common.del') . trans('common.success'),
                route('Admin::MessageBoardLog::index'));
        } else {
            return $this->error(trans('common.messageboard') . trans('common.log') . trans('common.del') . trans('common.error'),
                route('Admin::MessageBoardLog::index'));
        }
    }
}
