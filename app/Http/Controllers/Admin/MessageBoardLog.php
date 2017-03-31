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
        $whereValue = request('audit_id');
        $whereValue && $where[] = [
            'audit_id',
            'in',
            Model\Admin::where(['admin_name' => ['like', '%' . $whereValue . '%']])->select(['id'])->pluck('id'),
        ];

        $messageBoardLogList = Model\MessageBoardLog::where(function ($query) {
            $mb_id = request('mb_id');
            if ($mb_id) {
                $query->where('mb_id', '=', $mb_id);
            }

            $audit_id = request('audit_id');
            if ($audit_id) {
                $ids = Model\Admin::where([
                    [
                        'admin_name',
                        'like',
                        '%' . $audit_id . '%',
                    ],
                ])->select(['id'])->pluck('id');
                $query->whereIn('admin_id', $ids);
            }

            $created_at = mMktimeRange('created_at');
            if ($created_at) {
                $query->timeWhere('created_at', $created_at);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($messageBoardLogList as &$messageBoardLog) {
            $adminInfo = Model\Admin::colWhere($messageBoardLog['audit_id'])->first();
            if (null === $adminInfo) {
                $messageBoardLog['admin_name'] = trans('common.none') . trans('common.audit');
            } else {
                $messageBoardLog['admin_name'] = $adminInfo['admin_name'];
            }

            $memberInfo = Model\Member::colWhere($messageBoardLog['send_id'])->first();
            if (null === $memberInfo) {
                $messageBoardLog['member_name'] = trans('common.member') . trans('common.dont') . trans('common.exists');
            } else {
                $messageBoardLog['member_name'] = $memberInfo['member_name'];
            }

        }
        $assign['message_board_log_list'] = $messageBoardLogList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['audit_id']   = [
            'type' => 'input',
            'name' => trans('common.audit') . trans('common.admin') . trans('common.name'),
        ];
        $whereInfo['created_at'] = ['type' => 'time', 'name' => trans('common.send') . trans('common.time')];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['edit']    = $this->_check_privilege('Admin::MessageBoardLog::edit');
        $batchHandle['del']     = $this->_check_privilege('Admin::MessageBoardLog::del');
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
            $data     = [];
            $audit_id = request('audit_id');
            if (null !== $audit_id) {
                $data['audit_id'] = $audit_id ? session('backend_info.id') : 0;
            }

            $reply_info = request('reply_info');
            if (null !== $reply_info) {
                $data['reply_info'] = $reply_info;
            }

            $resultEdit = false;
            Model\MessageBoardLog::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                $preifxLang = $data['audit_id'] ? '' : trans('common.cancel');
                return $this->success($preifxLang . trans('common.audit') . trans('common.success'),
                    route('Admin::MessageBoardLog::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::MessageBoardLog::index') : route('Admin::MessageBoardLog::edit',
                    ['id' => $id]);
                return $this->error(trans('common.audit') . trans('common.error'), $errorGoLink);
            }
        }


        $editInfo  = Model\MessageBoardLog::colWhere($id)->first();
        $adminInfo = Model\Admin::colWhere($editInfo['audit_id'])->first();
        if (null === $adminInfo) {
            $editInfo['admin_name'] = trans('common.none') . trans('common.audit');
        } else {
            $editInfo['admin_name'] = $adminInfo['admin_name'];
        }
        $memberInfo = Model\Member::colWhere($editInfo['send_id'])->first();
        if (null === $memberInfo) {
            $editInfo['member_name'] = trans('common.member') . trans('common.dont') . trans('common.exists');
        } else {
            $editInfo['member_name'] = $memberInfo['member_name'];
        }
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
