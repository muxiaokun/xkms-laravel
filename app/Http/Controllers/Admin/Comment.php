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
        $commentList              = Model\Comment::where(function ($query) {
            $admin_name = request('admin_name');
            if ($admin_name) {
                $adminIds = Model\Admin::where('admin_name', 'like',
                    '%' . $admin_name . '%')->select(['id'])->pluck('id');
                $query->whereIn('audit_id', $adminIds);
            }

            $member_name = request('member_name');
            if ($member_name) {
                $memberIds = Model\Member::where('member_name', 'like',
                    '%' . $member_name . '%')->select(['id'])->pluck('id');
                $query->whereIn('send_id', $memberIds);
            }

            $route = request('route');
            if ($route) {
                $query->where('route', '=', $route);
            }

            $item = request('item');
            if ($item) {
                $query->where('item', '=', $item);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($commentList as &$comment) {
            if ($comment['audit_id']) {
                $adminInfo             = Model\Admin::colWhere($comment['audit_id'])->first();
                $comment['admin_name'] = (null === $adminInfo) ? $comment['audit_id'] : $adminInfo['admin_name'];
            } else {
                $comment['admin_name'] = trans('common.none') . trans('common.audit');
            }
            $memberInfo             = Model\Member::colWhere($comment['send_id'])->first();
            $comment['member_name'] = (null === $memberInfo) ? $comment['send_id'] : $memberInfo['member_name'];
        }
        $assign['comment_list'] = $commentList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['admin_name']  = [
            'type' => 'input',
            'name' => trans('common.audit') . trans('common.admin') . trans('common.name'),
        ];
        $whereInfo['member_name'] = [
            'type' => 'input',
            'name' => trans('common.send') . trans('common.member') . trans('common.name'),
        ];
        $whereInfo['route']       = ['type' => 'input', 'name' => trans('common.route')];
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

    //配置评论
    public function add()
    {
        if (request()->isMethod('POST')) {
            //表单提交的名称
            $col = [
                'comment_switch',
                'comment_allow',
                'comment_anony',
                'comment_interval',
            ];
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
        $data = ['audit_id' => session('backend_info.id')];

        $resultEdit = false;
        Model\Comment::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
            $resultEdit = $item->update($data);
            return $resultEdit;
        });
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
