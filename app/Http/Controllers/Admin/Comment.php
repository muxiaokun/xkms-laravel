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
// 后台 评论

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Comment extends Backend
{
    //列表
    public function index()
    {
        $AdminModel   = D('Admin');
        $MemberModel  = D('Member');
        $CommentModel = D('Comment');
        $where        = array();

        //建立where
        $v_value                       = '';
        $v_value                       = I('audit_id');
        $v_value && $where['audit_id'] = array(
            'in',
            $AdminModel->where(array('admin_name' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );
        $v_value                      = I('send_id');
        $v_value && $where['send_id'] = array(
            'in',
            $MemberModel->where(array('member_name' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );
        $v_value                         = I('controller');
        $v_value && $where['controller'] = $v_value;
        $v_value                         = I('item');
        $v_value && $where['item']       = $v_value;

        $comment_list = $CommentModel->order('add_time desc')->m_select($where, true);
        foreach ($comment_list as &$comment) {
            $comment['audit_name']  = ($comment['audit_id']) ? $AdminModel->m_find_column($comment['audit_id'], 'admin_name') : L('none') . L('audit');
            $member_name            = $MemberModel->m_find_column($comment['member_id'], 'member_name');
            $comment['member_name'] = ($member_name) ? $member_name : L('anonymous');
        }
        $this->assign('comment_list', $comment_list);
        $this->assign('comment_list_count', $CommentModel->get_page_count($where));

        //初始化where_info
        $where_info               = array();
        $where_info['audit_id']   = array('type' => 'input', 'name' => L('audit') . L('admin') . L('name'));
        $where_info['send_id']    = array('type' => 'input', 'name' => L('send') . L('member') . L('name'));
        $where_info['controller'] = array('type' => 'input', 'name' => L('controller'));
        $where_info['item']       = array('type' => 'input', 'name' => L('id'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('comment') . L('management'));
        $this->display();
    }

    //审核回复
    public function add()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = array(
                'COMMENT_SWITCH',
                'COMMENT_ALLOW',
                'COMMENT_ANONY',
                'COMMENT_INTERVAL',
            );
            $_POST['allow'] = explode(',', I('allow'));
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', L('config') . L('comment'));
        $this->display();
    }

    //审核
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $CommentModel = D('Comment');
        $data         = array('audit_id' => session('backend_info.id'));
        $result_edit  = $CommentModel->m_edit($id, $data);
        if ($result_edit) {
            $this->success(L('comment') . L('audit') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('comment') . L('audit') . L('error'), U('index'));
        }
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $CommentModel = D('Comment');
        $result_del   = $CommentModel->m_del($id);
        if ($result_del) {
            $this->success(L('comment') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('comment') . L('del') . L('error'), U('index'));
        }
    }
}
