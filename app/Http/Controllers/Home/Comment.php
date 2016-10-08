<?php
// 前台 评论

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Comment extends Frontend
{
    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        if (!config('COMMENT_SWITCH')) {
            exit();
        }

        switch ($field) {
            case 'put_data':
                $CommentModel = D('Comment');
                $data         = $this->makeData();
                if (!config('COMMENT_ANONY') && 0 == $data['send_id']) {
                    $result = array('status' => false, 'info' => trans('comment_error2'));
                    break;
                }
                if (!in_array($data['controller'], config('COMMENT_ALLOW')) || !$data['item']) {
                    $result = array('status' => false, 'info' => trans('comment_error3'));
                    break;
                }
                if (20 > strlen($data['content'])) {
                    $result = array('status' => false, 'info' => trans('comment_error5'));
                    break;
                }
                $where = array(
                    'send_id'    => $data['send_id'],
                    'controller' => $data['controller'],
                    'item'       => $data['item'],
                    'add_ip'     => array('exp', '= inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")'),
                    'add_time'   => array('gt', time() - config('COMMENT_INTERVAL')),
                );
                $countComment = $CommentModel->where($where)->count();
                if (0 < $countComment) {
                    $result = array('status' => false, 'info' => trans('comment_error4'));
                    break;
                }

                $addResult     = $CommentModel->mAdd($data);
                $result['info'] = ($addResult) ? trans('send') . trans('success') : trans('send') . trans('error');
                break;
            case 'get_data':
                if (!$data['controller'] || !$data['item']) {
                    break;
                }

                $where = array(
                    'controller' => $data['controller'],
                    'item'       => $data['item'],
                    'audit_id'   => array('gt', 0),
                );
                $MemberModel  = D('Member');
                $CommentModel = D('Comment');
                $commentList = $CommentModel->mSelect($where, true);
                foreach ($commentList as &$comment) {
                    $memberName            = $MemberModel->mFindColumn($comment['member_id'], 'member_name');
                    $comment['member_name'] = ($memberName) ? $memberName : trans('anonymous');
                }
                $this->assign('comment_list', $commentList);
                $this->assign('comment_list_count', $CommentModel->mGetPageCount($where));
                $this->display('index');
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

        $data                                     = request('data');
        (null !== $memberId) && $data['send_id'] = $memberId;

        return $data;
    }

}
