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
// 后台 考核

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Assess extends Backend
{
    //列表
    public function index()
    {
        $AssessModel      = D('Assess');
        $MemberGroupModel = D('MemberGroup');
        $where            = array();
        //建立where
        $v_value                          = '';
        $v_value                          = I('title');
        $v_value && $where['title']       = array('like', '%' . $v_value . '%');
        $v_value                          = I('group_level');
        $v_value && $where['group_level'] = $MemberGroupModel->where(array('name' => array('like', '%' . $v_value . '%')))->col_arr('id');
        $v_value                          = M_mktime_range('start_time');
        $v_value && $where['start_time']  = $v_value;
        $v_value                          = I('is_enable');
        $v_value && $where['is_enable']   = (1 == $v_value) ? 1 : 0;

        //初始化翻页 和 列表数据
        $assess_list = $AssessModel->m_select($where, true);
        foreach ($assess_list as &$assess) {
            $assess['group_name'] = ($assess['group_level']) ? $MemberGroupModel->m_find_column($assess['group_level'], 'name') : L('empty');
        }
        $this->assign('assess_list', $assess_list);
        $this->assign('assess_list_count', $AssessModel->get_page_count($where));

        //初始化where_info
        $where_info                = array();
        $where_info['title']       = array('type' => 'input', 'name' => L('title'));
        $where_info['group_level'] = array('type' => 'input', 'name' => L('assess') . L('group'));
        $where_info['start_time']  = array('type' => 'time', 'name' => L('add') . L('time'));
        $where_info['is_enable']   = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('enable'), 2 => L('disable')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle             = array();
        $batch_handle['add']      = $this->_check_privilege('add');
        $batch_handle['edit']     = $this->_check_privilege('edit');
        $batch_handle['log_edit'] = $this->_check_privilege('edit', 'AssessLog');
        $batch_handle['del']      = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('assess') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $AssessModel = D('Assess');
            $data        = $this->_make_data();
            $result_add  = $AssessModel->m_add($data);
            if ($result_add) {
                $this->success(L('assess') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('assess') . L('add') . L('error'), U('add'));
            }
        }

        $this->assign('title', L('assess') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AssessModel = D('Assess');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $AssessModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('assess') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('assess') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info               = $AssessModel->m_find($id);
        $MemberGroupModel        = D('MemberGroup');
        $edit_info['group_name'] = $MemberGroupModel->m_find_column($edit_info['group_level'], 'name');
        $this->assign('edit_info', $edit_info);

        $this->assign('title', L('assess') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AssessModel = D('Assess');
        $result_del  = $AssessModel->m_del($id);
        if ($result_del) {
            $this->success(L('assess') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('assess') . L('del') . L('error'), U('index'));
        }
    }

    //异步数据获取
    protected function _get_data($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'group_level':
                isset($data['inserted']) && $where['id']  = array('not in', $data['inserted']);
                isset($data['keyword']) && $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $MemberGroupModel                         = D('MemberGroup');
                $member_group_list                        = $MemberGroupModel->m_select($where);
                foreach ($member_group_list as $member_group) {
                    $result['info'][] = array('value' => $member_group['id'], 'html' => $member_group['name']);
                }
                break;
        }

        return $result;
    }

    //构造数据
    private function _make_data()
    {
        //初始化参数
        $id            = I('get.id');
        $title         = I('title');
        $explains      = I('explains');
        $group_level   = I('group_level');
        $start_time    = I('start_time');
        $end_time      = I('end_time');
        $start_time    = M_mktime($start_time, true);
        $end_time      = M_mktime($end_time, true);
        $is_enable     = I('is_enable');
        $target        = I('target');
        $ext_info      = array();
        $grade_project = array();
        foreach (I('ext_info') as $value) {
            $grade_project[] = json_decode(str_replace('&quot;', '"', $value), true);
        }
        $ext_info = json_encode($grade_project);

        $data                                                                   = array();
        ('add' == ACTION_NAME || null !== $title) && $data['title']             = $title;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains']       = $explains;
        ('add' == ACTION_NAME || null !== $group_level) && $data['group_level'] = $group_level;
        ('add' == ACTION_NAME || null !== $start_time) && $data['start_time']   = $start_time;
        ('add' == ACTION_NAME || null !== $end_time) && $data['end_time']       = $end_time;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable']     = $is_enable;
        ('add' == ACTION_NAME || null !== $target) && $data['target']           = $target;
        ('add' == ACTION_NAME || null !== $ext_info) && $data['ext_info']       = $ext_info;
        return $data;
    }
}
