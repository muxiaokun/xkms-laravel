<?php
// 前台 评论

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;
use App\Model;
use Carbon\Carbon;

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
                $data = $this->makeData();
                if (!config('system.comment_anony') && 0 == $data['send_id']) {
                    $result = ['status' => false, 'info' => trans('comment.comment_error2')];
                    break;
                }
                $commentAllow = explode(',', config('system.comment_allow'));
                if (!in_array($data['route'], $commentAllow) || !$data['item']) {
                    $result = ['status' => false, 'info' => trans('comment.comment_error3')];
                    break;
                }
                if (20 > strlen($data['content'])) {
                    $result = ['status' => false, 'info' => trans('comment.comment_error5')];
                    break;
                }
                $where[] = ['send_id', '=', $data['send_id']];
                $where[] = ['route', '=', $data['route']];
                $where[] = ['item', '=', $data['item']];
                $where[] = ['add_ip', '=', $data['add_ip']];
                $where[] = ['created_at', '>', Carbon::now()->subSecond(config('system.comment_interval'))];
                $countComment = Model\Comment::where($where)->count();
                if (0 < $countComment) {
                    $result = ['status' => false, 'info' => trans('comment.comment_error4')];
                    break;
                }

                $addResult      = Model\Comment::create($data);
                $result['info'] = ($addResult) ? trans('common.send') . trans('common.success') : trans('common.send') . trans('common.error');
                break;
            case 'get_data':
                if (!$data['route'] || !$data['item']) {
                    break;
                }

                $where[] = ['route', '=', $data['route']];
                $where[] = ['item', '=', $data['item']];
                $where[] = ['audit_id', '>', 0];

                $commentList = Model\Comment::where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
                foreach ($commentList as &$comment) {
                    $comment['member_name'] = Model\Member::colWhere($comment['member_id'])->get()->implode('member_name',
                        ' | ');
                    !$comment['member_name'] = trans('common.anonymous');
                }
                $assign['comment_list'] = $commentList;
                return view('home.Comment_index', $assign);
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
        $data['add_ip'] = request()->ip();

        return $data;
    }

}
