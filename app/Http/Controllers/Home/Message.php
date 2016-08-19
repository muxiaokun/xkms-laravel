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
//前台 站内信

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Message extends FrontendMember
{
    public function index()
    {
        $member_id = session('frontend_info.id');
        $this->assign('member_id', $member_id);

        $MemberModel  = D('Member');
        $MessageModel = D('Message');
        $where        = array();
        //0为系统发送/接收
        $where['_complex']['_logic']     = 'OR';
        $where['_complex']['receive_id'] = $where['_complex']['send_id'] = $member_id;
        //建立where
        $v_value                         = '';
        $v_value                         = I('receive_id');
        $v_value && $where['receive_id'] = array(
            'in',
            $MemberModel->where(array('member_name' => array('like', '%' . $v_value . '%')))->col_arr('id'),
        );
        $v_value                        = M_mktime_range('send_time');
        $v_value && $where['send_time'] = $v_value;

        $message_list = $MessageModel->order('receive_time asc,send_time desc')->m_select($where, true);
        foreach ($message_list as &$message) {
            $message['send_name']    = ($message['send_id']) ? $MemberModel->m_find_column($message['send_id'], 'member_name') : L('system');
            $message['receive_name'] = ($message['receive_id']) ? $MemberModel->m_find_column($message['receive_id'], 'member_name') : L('system');
        }
        $this->assign('message_list', $message_list);
        $this->assign('message_list_count', $MessageModel->get_page_count($where));

        //初始化where_info
        $where_info               = array();
        $where_info['receive_id'] = array('type' => 'input', 'name' => L('receive') . L('member'));
        $where_info['send_time']  = array('type' => 'time', 'name' => L('send') . L('time'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle        = array();
        $batch_handle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('message'));
        $this->display();
    }

    //发送信息
    public function add()
    {
        $receive_id   = I('receive_id');
        $MessageModel = D('Message');
        if (IS_POST) {
            $content = I('content');
            if (null == $content) {
                $this->error(L('content') . L('not') . L('empty'), U('index'));
            }

            if (null == $receive_id) {
                $this->error(L('receive') . L('member') . L('error'), U('index'));
            }

            $data = array(
                'send_id'    => session('frontend_info.id'),
                'receive_id' => $receive_id,
                'content'    => $content,
            );
            $result_add = $MessageModel->m_add($data);
            if ($result_add) {
                $this->success(L('send') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('send') . L('error'), U('index'));
            }
        }

        if ($receive_id) {
            $MemberModel = D('Member');
            $this->assign('receive_info', $MemberModel->m_find($receive_id));
        }

        $this->assign('title', L('send') . L('message'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MessageModel = D('Message');
        $result_del   = $MessageModel->m_del($id);
        if ($result_del) {
            $this->success(L('message') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('message') . L('del') . L('error'), U('index'));
        }
    }

    //异步获取数据接口
    protected function _get_data($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'receive_id':
                isset($data['keyword']) && $where['member_name'] = array('like', '%' . $data['keyword'] . '%');
                isset($data['inserted']) && $where['id']         = array('not in', $data['inserted']);
                $MemberModel                                     = D('Member');
                $member_user_list                                = $MemberModel->m_select($where);
                $result['info'][]                                = array('value' => 0, 'html' => L('system'));
                foreach ($member_user_list as $member_user) {
                    $result['info'][] = array('value' => $member_user['id'], 'html' => $member_user['member_name']);
                }
                break;
            case 'read_message':
                $MessageModel = D('Message');
                $current_time = time();
                $member_id    = session('frontend_info.id');
                $where        = array('receive_id' => $member_id);
                $result_edit  = $MessageModel->where($where)->m_edit($data['id'], array('receive_time' => $current_time));
                if ($result_edit) {
                    $result['info'] = date(C('SYS_DATE_DETAIL'), $current_time);
                } else {
                    $result['status'] = false;
                }
                break;
        }

        return $result;
    }
}
