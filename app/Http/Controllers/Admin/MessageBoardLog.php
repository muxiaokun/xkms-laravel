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
        $where                = [];
        //如果$msgId不存在就返回全部留言
        $msgId = request('msg_id');
        $msgId && $where['msg_id'] = $msgId;

        //建立where
        $whereValue = '';
        $whereValue = request('audit_id');
        $whereValue && $where['audit_id'] = [
            'in',
            Model\Admins::where(['admin_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where['add_time'] = $whereValue;

        $messageBoardLogList = Model\MessageBoardLog::order('add_time desc')->mSelect($where, true);
        foreach ($messageBoardLogList as &$messageBoardLog) {
            $messageBoardLog['admin_name']  = ($messageBoardLog['audit_id']) ? Model\Admins::mFindColumn($messageBoardLog['audit_id'],
                'admin_name') : trans('common.none') . trans('common.audit');
            $memberName                     = Model\Member::mFindColumn($messageBoardLog['send_id'], 'member_name');
            $messageBoardLog['member_name'] = ($memberName) ? $memberName : trans('common.empty');
        }
        $assign['message_board_log_list']       = $messageBoardLogList;
        $assign['message_board_log_list_count'] = Model\MessageBoardLog::mGetPageCount($where);

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
        return view('admin.', $assign);
    }

    //审核回复
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if (request()->isMethod('POST')) {
            $data       = [
                'audit_id'   => session('backend_info.id'),
                'reply_info' => request('reply_info'),
            ];
            $resultEdit = Model\MessageBoardLog::mEdit($id, $data);
            if ($resultEdit) {
                return $this->success(trans('common.audit') . trans('common.success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                return $this->error(trans('common.audit') . trans('common.error'), $errorGoLink);
            }
        }


        $editInfo                = Model\MessageBoardLog::mFind($id);
        $editInfo['admin_name']  = ($editInfo['audit_id']) ? Model\Admins::mFindColumn($editInfo['audit_id'],
            'admin_name') : trans('common.none') . trans('common.audit');
        $memberName              = Model\Member::mFindColumn($editInfo['send_id'], 'member_name');
        $editInfo['member_name'] = ($memberName) ? $memberName : trans('common.empty');
        $editInfo['send_info']   = json_decode($editInfo['send_info'], true);
        $assign['edit_info']     = $editInfo;

        $assign['title'] = trans('common.messageboard') . trans('common.audit');
        return view('admin.', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\MessageBoardLog::mDel($id);
        if ($resultDel) {
            return $this->success(trans('common.messageboard') . trans('common.log') . trans('common.del') . trans('common.success'),
                route('index'));
            return;
        } else {
            return $this->error(trans('common.messageboard') . trans('common.log') . trans('common.del') . trans('common.error'),
                route('index'));
        }
    }
}
