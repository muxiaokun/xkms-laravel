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
// 后台 留言板

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class MessageBoard extends Backend
{
    //列表
    public function index()
    {
        $MessageBoardModel = D('MessageBoard');
        $where             = array();

        //建立where
        $v_value                   = '';
        $v_value                   = I('name');
        $v_value && $where['name'] = array('like', '%' . $v_value . '%');

        $message_board_list = $MessageBoardModel->m_select($where, true);
        foreach ($message_board_list as &$message_board) {
            $option = array();
            foreach ($message_board['config'] as $name => $value) {
                $option[] = $name;
            }
            $message_board['option'] = M_substr(implode(',', $option), 40);
        }
        $this->assign('message_board_list', $message_board_list);
        $this->assign('message_board_list_count', $MessageBoardModel->get_page_count($where));

        //初始化batch_handle
        $batch_handle              = array();
        $batch_handle['log_index'] = $this->_check_privilege('index', 'MessageBoardLog');
        $batch_handle['add']       = $this->_check_privilege('add');
        $batch_handle['edit']      = $this->_check_privilege('edit');
        $batch_handle['del']       = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('messageboard') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $data              = $this->_make_data();
            $MessageBoardModel = D('MessageBoard');
            $result_add        = $MessageBoardModel->m_add($data);
            if ($result_add) {
                $this->success(L('messageboard') . L('add') . L('success'), $reback_link);
                return;
            } else {
                $this->error(L('messageboard') . L('add') . L('error'), U('add', array('cate_id' => I('get.cate_id'))));
            }
        }

        $this->assign('template_list', M_scan_template('index', C('DEFAULT_MODULE'), 'MessageBoard'));
        $this->assign('title', L('messageboard') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MessageBoardModel = D('MessageBoard');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $MessageBoardModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('messageboard') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('messageboard') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info           = $MessageBoardModel->m_find($id);
        $edit_info['config'] = json_encode($edit_info['config']);
        $this->assign('edit_info', $edit_info);

        $this->assign('template_list', M_scan_template('index', C('DEFAULT_MODULE'), 'MessageBoard'));
        $this->assign('title', L('messageboard') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $MessageBoardModel = D('MessageBoard');
        $result_del        = $MessageBoardModel->m_del($id);
        if ($result_del) {
            $this->success(L('messageboard') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('messageboard') . L('del') . L('error'), U('index'));
        }
    }

    //构造数据
    private function _make_data()
    {
        $name     = I('name');
        $template = I('template');
        $config   = json_decode(htmlspecialchars_decode(I('config')), true);

        $data                                                             = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']         = $name;
        ('add' == ACTION_NAME || null !== $template) && $data['template'] = $template;
        ('add' == ACTION_NAME || null !== $config) && $data['config']     = $config;

        return $data;
    }
}
