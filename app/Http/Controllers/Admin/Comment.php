<?php
// 后台 评论

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Comment extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('audit_id');
        $whereValue && $where[] = [
            'audit_id',
            'in',
            Model\Admins::where(['admin_name' => ['like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];
        $whereValue = request('send_id');
        $whereValue && $where[] = [
            'send_id',
            'in',
            Model\Member::where(['member_name' => ['like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];
        $whereValue = request('controller');
        $whereValue && $where[] = ['controller', $whereValue];
        $whereValue = request('item');
        $whereValue && $where[] = ['item', $whereValue];

        $commentList            = Model\Comment::orderBy('add_time',
            'desc')->where($where)->paginate(config('system.sys_max_row'));
        $assign['comment_list'] = $commentList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['audit_id']   = [
            'type' => 'input',
            'name' => trans('common.audit') . trans('common.admin') . trans('common.name'),
        ];
        $whereInfo['send_id']    = [
            'type' => 'input',
            'name' => trans('common.send') . trans('common.member') . trans('common.name'),
        ];
        $whereInfo['controller'] = ['type' => 'input', 'name' => trans('common.controller')];
        $whereInfo['item']       = ['type' => 'input', 'name' => trans('common.id')];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.comment') . trans('common.management');
        return view('admin.Comment_index', $assign);
    }

    //审核回复
    public function add()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col            = [
                'COMMENT_SWITCH',
                'COMMENT_ALLOW',
                'COMMENT_ANONY',
                'COMMENT_INTERVAL',
            ];
            $_POST['allow'] = explode(',', request('allow'));
            return $this->_put_config($col, 'system');
        }

        $assign['title'] = trans('common.config') . trans('common.comment');
        return view('admin.Comment_add', $assign);
    }

    //审核
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Comment::index'));
        }

        $data       = ['audit_id' => session('backend_info.id')];
        $resultEdit = Model\Comment::idWhere($id)->update($data);
        if ($resultEdit) {
            return $this->success(trans('common.comment') . trans('common.audit') . trans('common.success'),
                route('Admin::Comment::index'));
        } else {
            return $this->error(trans('common.comment') . trans('common.audit') . trans('common.error'),
                route('Admin::Comment::index'));
        }
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Comment::index'));
        }

        $resultDel = Model\Comment::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.comment') . trans('common.del') . trans('common.success'),
                route('Admin::Comment::index'));
        } else {
            return $this->error(trans('common.comment') . trans('common.del') . trans('common.error'),
                route('Admin::Comment::index'));
        }
    }
}
