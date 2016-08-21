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
// 前台 评论

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Frontend;

class Comment extends Frontend
{
    //异步获取数据接口
    protected function _get_data($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        if (!C('COMMENT_SWITCH')) {
            exit();
        }

        switch ($field) {
            case 'put_data':
                $CommentModel = D('Comment');
                $data         = $this->_make_data();
                if (!C('COMMENT_ANONY') && 0 == $data['send_id']) {
                    $result = array('status' => false, 'info' => L('comment_error2'));
                    break;
                }
                if (!in_array($data['controller'], C('COMMENT_ALLOW')) || !$data['item']) {
                    $result = array('status' => false, 'info' => L('comment_error3'));
                    break;
                }
                if (20 > strlen($data['content'])) {
                    $result = array('status' => false, 'info' => L('comment_error5'));
                    break;
                }
                $where = array(
                    'send_id'    => $data['send_id'],
                    'controller' => $data['controller'],
                    'item'       => $data['item'],
                    'add_ip'     => array('exp', '= inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")'),
                    'add_time'   => array('gt', time() - C('COMMENT_INTERVAL')),
                );
                $count_comment = $CommentModel->where($where)->count();
                if (0 < $count_comment) {
                    $result = array('status' => false, 'info' => L('comment_error4'));
                    break;
                }

                $add_result     = $CommentModel->mAdd($data);
                $result['info'] = ($add_result) ? L('send') . L('success') : L('send') . L('error');
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
                $comment_list = $CommentModel->mSelect($where, true);
                foreach ($comment_list as &$comment) {
                    $member_name            = $MemberModel->mFindColumn($comment['member_id'], 'member_name');
                    $comment['member_name'] = ($member_name) ? $member_name : L('anonymous');
                }
                $this->assign('comment_list', $comment_list);
                $this->assign('comment_list_count', $CommentModel->getPageCount($where));
                $this->display('index');
                exit();
                break;
        }
        return $result;
    }

    //建立数据
    private function _make_data()
    {
        $member_id = session('frontend_info.id');
        $member_id = ($member_id) ? $member_id : 0;

        $data                                     = I('data');
        (null !== $member_id) && $data['send_id'] = $member_id;

        return $data;
    }

}
