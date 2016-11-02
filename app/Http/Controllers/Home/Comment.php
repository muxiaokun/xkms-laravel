<?php
// 前台 评论

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;

class Comment extends Frontend
{
    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        if (!config('system.comment_switch')) {
            exit();
        }

        switch ($field) {
            case 'put_data':
                $data         = $this->makeData();
                if (!config('system.comment_anony') && 0 == $data['send_id']) {
                    $result = ['status' => false, 'info' => trans('common.comment_error2')];
                    break;
                }
                if (!in_array($data['controller'], config('system.comment_allow')) || !$data['item']) {
                    $result = ['status' => false, 'info' => trans('common.comment_error3')];
                    break;
                }
                if (20 > strlen($data['content'])) {
                    $result = ['status' => false, 'info' => trans('common.comment_error5')];
                    break;
                }
                $where        = [
                    'send_id'    => $data['send_id'],
                    'controller' => $data['controller'],
                    'item'       => $data['item'],
                    'add_ip'     => request()->ip(),
                    'add_time'   => ['gt', Carbon::now() - config('system.comment_interval')],
                ];
                $countComment = Model\Comment::where($where)->count();
                if (0 < $countComment) {
                    $result = ['status' => false, 'info' => trans('common.comment_error4')];
                    break;
                }

                $addResult      = Model\Comment::mAdd($data);
                $result['info'] = ($addResult) ? trans('common.send') . trans('common.success') : trans('common.send') . trans('common.error');
                break;
            case 'get_data':
                if (!$data['controller'] || !$data['item']) {
                    break;
                }

                $where       = [
                    'controller' => $data['controller'],
                    'item'       => $data['item'],
                    'audit_id'   => ['gt', 0],
                ];
                $commentList = Model\Comment::mList($where, true);
                foreach ($commentList as &$comment) {
                    $memberName             = Model\Member::mFindColumn($comment['member_id'], 'member_name');
                    $comment['member_name'] = ($memberName) ? $memberName : trans('common.anonymous');
                }
                $assign['comment_list']       = $commentList;
                $assign['comment_list_count'] = Model\Comment::mGetPageCount($where);
                return view('home.index', $assign);
                exit();
                break;
        }
        return $result;
    }

    //建立数据
    private function makeData()
    {
        $memberId = session('frontend_info.id');
        $memberId = ($memberId) ? $memberId : 0;

        $data = request('data');
        (null !== $memberId) && $data['send_id'] = $memberId;

        return $data;
    }

}
