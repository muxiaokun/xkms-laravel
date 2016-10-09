<?php
// 后台 留言板

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class MessageBoardLog extends Backend
{
    //列表
    public function index()
    {
        $AdminModel           = D('Admin');
        $MemberModel          = D('Member');
        $MessageBoardLogModel = D('MessageBoardLog');
        $where                = [];
        //如果$msgId不存在就返回全部留言
        $msgId = request('msg_id');
        $msgId && $where['msg_id'] = $msgId;

        //建立where
        $whereValue = '';
        $whereValue = request('audit_id');
        $whereValue && $where['audit_id'] = [
            'in',
            $AdminModel->where(['admin_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where['add_time'] = $whereValue;

        $messageBoardLogList = $MessageBoardLogModel->order('add_time desc')->mSelect($where, true);
        foreach ($messageBoardLogList as &$messageBoardLog) {
            $messageBoardLog['admin_name']  = ($messageBoardLog['audit_id']) ? $AdminModel->mFindColumn($messageBoardLog['audit_id'],
                'admin_name') : trans('none') . trans('audit');
            $memberName                     = $MemberModel->mFindColumn($messageBoardLog['send_id'], 'member_name');
            $messageBoardLog['member_name'] = ($memberName) ? $memberName : trans('empty');
        }
        $this->assign('message_board_log_list', $messageBoardLogList);
        $this->assign('message_board_log_list_count', $MessageBoardLogModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo             = [];
        $whereInfo['audit_id'] = ['type' => 'input', 'name' => trans('audit') . trans('admin') . trans('name')];
        $whereInfo['add_time'] = ['type' => 'time', 'name' => trans('send') . trans('time')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('messageboard') . trans('management'));
        $this->display();
    }

    //审核回复
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $MessageBoardLogModel = D('MessageBoardLog');
        if (IS_POST) {
            $data       = [
                'audit_id'   => session('backend_info.id'),
                'reply_info' => request('reply_info'),
            ];
            $resultEdit = $MessageBoardLogModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('audit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('audit') . trans('error'), $errorGoLink);
            }
        }

        $AdminModel  = D('Admin');
        $MemberModel = D('Member');

        $editInfo                = $MessageBoardLogModel->mFind($id);
        $editInfo['admin_name']  = ($editInfo['audit_id']) ? $AdminModel->mFindColumn($editInfo['audit_id'],
            'admin_name') : trans('none') . trans('audit');
        $memberName              = $MemberModel->mFindColumn($editInfo['send_id'], 'member_name');
        $editInfo['member_name'] = ($memberName) ? $memberName : trans('empty');
        $editInfo['send_info']   = json_decode($editInfo['send_info'], true);
        $this->assign('edit_info', $editInfo);

        $this->assign('title', trans('messageboard') . trans('audit'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $MessageBoardLogModel = D('MessageBoardLog');
        $resultDel            = $MessageBoardLogModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('messageboard') . trans('log') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('messageboard') . trans('log') . trans('del') . trans('error'), route('index'));
        }
    }
}
