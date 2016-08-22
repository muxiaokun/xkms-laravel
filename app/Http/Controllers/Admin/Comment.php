<?php
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
        $whereValue                       = '';
        $whereValue                       = I('audit_id');
        $whereValue && $where['audit_id'] = array(
            'in',
            $AdminModel->where(array('admin_name' => array('like', '%' . $whereValue . '%')))->mColumn2Array('id'),
        );
        $whereValue                      = I('send_id');
        $whereValue && $where['send_id'] = array(
            'in',
            $MemberModel->where(array('member_name' => array('like', '%' . $whereValue . '%')))->mColumn2Array('id'),
        );
        $whereValue                         = I('controller');
        $whereValue && $where['controller'] = $whereValue;
        $whereValue                         = I('item');
        $whereValue && $where['item']       = $whereValue;

        $commentList = $CommentModel->order('add_time desc')->mSelect($where, true);
        foreach ($commentList as &$comment) {
            $comment['audit_name']  = ($comment['audit_id']) ? $AdminModel->mFindColumn($comment['audit_id'], 'admin_name') : L('none') . L('audit');
            $memberName            = $MemberModel->mFindColumn($comment['member_id'], 'member_name');
            $comment['member_name'] = ($memberName) ? $memberName : L('anonymous');
        }
        $this->assign('comment_list', $commentList);
        $this->assign('comment_list_count', $CommentModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = array();
        $whereInfo['audit_id']   = array('type' => 'input', 'name' => L('audit') . L('admin') . L('name'));
        $whereInfo['send_id']    = array('type' => 'input', 'name' => L('send') . L('member') . L('name'));
        $whereInfo['controller'] = array('type' => 'input', 'name' => L('controller'));
        $whereInfo['item']       = array('type' => 'input', 'name' => L('id'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

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
        $resultEdit  = $CommentModel->mEdit($id, $data);
        if ($resultEdit) {
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
        $resultDel   = $CommentModel->mDel($id);
        if ($resultDel) {
            $this->success(L('comment') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('comment') . L('del') . L('error'), U('index'));
        }
    }
}
