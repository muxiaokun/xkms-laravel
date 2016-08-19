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
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Navigation extends Backend
{
    //导航级别 post_name
    private $navigation_config = array('navigation_level' => 4, 'post_name' => 'navigation_list');

    //列表
    public function index()
    {
        $NavigationModel = D('Navigation');
        //建立where
        $v_value                         = '';
        $v_value                         = I('name');
        $v_value && $where['name']       = array('like', '%' . $v_value . '%');
        $v_value                         = I('short_name');
        $v_value && $where['short_name'] = array('like', '%' . $v_value . '%');
        $v_value                         = I('is_enable');
        $v_value && $where['is_enable']  = (1 == $v_value) ? 1 : 0;
        //初始化翻页 和 列表数据
        $navigation_list = $NavigationModel->m_select($where, true);
        $this->assign('navigation_list', $navigation_list);
        $this->assign('navigation_list_count', $NavigationModel->get_page_count($where));

        //初始化where_info
        $where_info               = array();
        $where_info['name']       = array('type' => 'input', 'name' => L('navigation') . L('name'));
        $where_info['short_name'] = array('type' => 'input', 'name' => L('short') . L('name'));
        $where_info['is_enable']  = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('enable'), 2 => L('disable')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('navigation') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $NavigationModel = D('Navigation');
            $data            = $this->_make_data();
            $result_add      = $NavigationModel->m_add($data);
            if ($result_add) {
                $this->success(L('navigation') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('navigation') . L('add') . L('error'), U('add'));
            }
        }

        $this->assign('navigation_config', $this->navigation_config);
        $this->assign('title', L('add') . L('navigation'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $NavigationModel = D('Navigation');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $NavigationModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('navigation') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('navigation') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info = $NavigationModel->m_find($id);
        //$edit_info['ext_info'] = json_encode($edit_info['ext_info']);
        $this->assign('edit_info', $edit_info);

        $this->assign('navigation_config', $this->navigation_config);
        $this->assign('title', L('edit') . L('navigation'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $NavigationModel = D('Navigation');
        $result_del      = $NavigationModel->m_del($id);
        if ($result_del) {
            $this->success(L('navigation') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('navigation') . L('del') . L('error'), U('index'));
        }
    }

    //异步和表单数据验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $NavigationModel = D('Navigation');
                $itlink_info     = $NavigationModel->m_select(array('short_name' => $data['short_name'], 'id' => array('neq', $data['id'])));
                if (0 < count($itlink_info)) {
                    $result['info'] = L('short') . L('name') . L('exists');
                    break;
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //构造数据
    private function _make_data()
    {
        //初始化参数
        $id         = I('id');
        $name       = I('name');
        $short_name = I('short_name');
        $is_enable  = I('is_enable');
        $ext_info   = $this->_make_navigation(I($this->navigation_config['post_name']));

        //检测初始化参数是否合法
        $error_go_link = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $short_name) {
            $result = $this->_validform('short_name', array('id' => $id, 'short_name' => $short_name));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }

        $data                                                                 = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']             = $name;
        ('add' == ACTION_NAME || null !== $short_name) && $data['short_name'] = $short_name;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable']   = $is_enable;
        ('add' == ACTION_NAME || null !== $ext_info) && $data['ext_info']     = $ext_info;

        return $data;
    }

    //构造导航数据
    private function _make_navigation(&$data, $pid = 0)
    {
        $result = array();
        foreach ($data[$pid] as $nav) {
            $child              = json_decode(str_replace(array('&quot;', '&amp;'), array('"', '&'), $nav), true);
            $child['nav_child'] = $this->_make_navigation($data, $child['nav_id']);
            $result[]           = $child;
        }
        return json_encode($result);
    }
}
