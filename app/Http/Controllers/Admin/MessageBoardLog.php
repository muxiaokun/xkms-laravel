<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
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
        $where                = array();
        //如果$msg_id不存在就返回全部留言
        $msg_id                     = I('msg_id');
        $msg_id && $where['msg_id'] = $msg_id;

        //建立where
        $v_value                       = '';
        $v_value                       = I('audit_id');
        $v_value && $where['audit_id'] = array(
            'in',
            $AdminModel->where(array('admin_name' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );
        $v_value                       = M_mktime_range('add_time');
        $v_value && $where['add_time'] = $v_value;

        $message_board_log_list = $MessageBoardLogModel->order('add_time desc')->m_select($where, true);
        foreach ($message_board_log_list as &$message_board_log) {
            $message_board_log['admin_name']  = ($message_board_log['audit_id']) ? $AdminModel->m_find_column($message_board_log['audit_id'], 'admin_name') : L('none') . L('audit');
            $member_name                      = $MemberModel->m_find_column($message_board_log['send_id'], 'member_name');
            $message_board_log['member_name'] = ($member_name) ? $member_name : L('empty');
        }
        $this->assign('message_board_log_list', $message_board_log_list);
        $this->assign('message_board_log_list_count', $MessageBoardLogModel->get_page_count($where));

        //初始化where_info
        $where_info             = array();
        $where_info['audit_id'] = array('type' => 'input', 'name' => L('audit') . L('admin') . L('name'));
        $where_info['add_time'] = array('type' => 'time', 'name' => L('send') . L('time'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('messageboard') . L('management'));
        $this->display();
    }

    //审核回复
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MessageBoardLogModel = D('MessageBoardLog');
        if (IS_POST) {
            $data = array(
                'audit_id'   => session('backend_info.id'),
                'reply_info' => I('reply_info'),
            );
            $result_edit = $MessageBoardLogModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('audit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('audit') . L('error'), $error_go_link);
            }
        }

        $AdminModel  = D('Admin');
        $MemberModel = D('Member');

        $edit_info                = $MessageBoardLogModel->m_find($id);
        $edit_info['admin_name']  = ($edit_info['audit_id']) ? $AdminModel->m_find_column($edit_info['audit_id'], 'admin_name') : L('none') . L('audit');
        $member_name              = $MemberModel->m_find_column($edit_info['send_id'], 'member_name');
        $edit_info['member_name'] = ($member_name) ? $member_name : L('empty');
        $edit_info['send_info']   = json_decode($edit_info['send_info'], true);
        $this->assign('edit_info', $edit_info);

        $this->assign('title', L('messageboard') . L('audit'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MessageBoardLogModel = D('MessageBoardLog');
        $result_del           = $MessageBoardLogModel->m_del($id);
        if ($result_del) {
            $this->success(L('messageboard') . L('log') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('messageboard') . L('log') . L('del') . L('error'), U('index'));
        }
    }
}
