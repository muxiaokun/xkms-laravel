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
        $where        = [];

        //建立where
        $whereValue = '';
        $whereValue = request('audit_id');
        $whereValue && $where['audit_id'] = [
            'in',
            $AdminModel->where(['admin_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = request('send_id');
        $whereValue && $where['send_id'] = [
            'in',
            $MemberModel->where(['member_name' => ['like', '%' . $whereValue . '%']])->mColumn2Array('id'),
        ];
        $whereValue = request('controller');
        $whereValue && $where['controller'] = $whereValue;
        $whereValue = request('item');
        $whereValue && $where['item'] = $whereValue;

        $commentList = $CommentModel->order('add_time desc')->mSelect($where, true);
        foreach ($commentList as &$comment) {
            $comment['audit_name']  = ($comment['audit_id']) ? $AdminModel->mFindColumn($comment['audit_id'],
                'admin_name') : trans('none') . trans('audit');
            $memberName             = $MemberModel->mFindColumn($comment['member_id'], 'member_name');
            $comment['member_name'] = ($memberName) ? $memberName : trans('anonymous');
        }
        $this->assign('comment_list', $commentList);
        $this->assign('comment_list_count', $CommentModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['audit_id']   = ['type' => 'input', 'name' => trans('audit') . trans('admin') . trans('name')];
        $whereInfo['send_id']    = ['type' => 'input', 'name' => trans('send') . trans('member') . trans('name')];
        $whereInfo['controller'] = ['type' => 'input', 'name' => trans('controller')];
        $whereInfo['item']       = ['type' => 'input', 'name' => trans('id')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('comment') . trans('management'));
        $this->display();
    }

    //审核回复
    public function add()
    {
        if (IS_POST) {
            //表单提交的名称
            $col            = [
                'COMMENT_SWITCH',
                'COMMENT_ALLOW',
                'COMMENT_ANONY',
                'COMMENT_INTERVAL',
            ];
            $_POST['allow'] = explode(',', request('allow'));
            $this->_put_config($col, 'system');
            return;
        }

        $this->assign('title', trans('config') . trans('comment'));
        $this->display();
    }

    //审核
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $CommentModel = D('Comment');
        $data         = ['audit_id' => session('backend_info.id')];
        $resultEdit   = $CommentModel->mEdit($id, $data);
        if ($resultEdit) {
            $this->success(trans('comment') . trans('audit') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('comment') . trans('audit') . trans('error'), route('index'));
        }
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $CommentModel = D('Comment');
        $resultDel    = $CommentModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('comment') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('comment') . trans('del') . trans('error'), route('index'));
        }
    }
}
