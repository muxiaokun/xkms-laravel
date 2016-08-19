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
//后台 地域

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Region extends Backend
{
    //列表
    public function index()
    {
        $RegionModel = D('Region');
        //建立where
        $v_value                          = '';
        $v_value                          = I('region_name');
        $v_value && $where['region_name'] = array('like', '%' . $v_value . '%');
        $v_value                          = I('short_spell');
        $v_value && $where['short_spell'] = array('like', '%' . $v_value . '%');
        $v_value                          = I('areacode');
        $v_value && $where['areacode']    = array('like', '%' . $v_value . '%');
        $v_value                          = I('postcode');
        $v_value && $where['postcode']    = array('like', '%' . $v_value . '%');

        //初始化翻页 和 列表数据
        $region_list = $RegionModel->m_select($where, true);
        foreach ($region_list as &$region) {
            $region['parent_name'] = $RegionModel->m_find_column($region['parent_id'], 'region_name');
        }

        $this->assign('region_list', $region_list);
        $this->assign('region_list_count', $RegionModel->get_page_count($where));

        //初始化where_info
        $where_info['region_name'] = array('type' => 'input', 'name' => L('region_name'));
        $where_info['short_spell'] = array('type' => 'input', 'name' => L('short_spell'));
        $where_info['areacode']    = array('type' => 'input', 'name' => L('areacode'));
        $where_info['postcode']    = array('type' => 'input', 'name' => L('postcode'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('region') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $RegionModel = D('Region');
            $data        = $this->_make_data();
            $result_add  = $RegionModel->m_add($data);
            if ($result_add) {
                $this->success(L('region') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('region') . L('add') . L('error'), U('add'));
            }
        }

        $this->assign('title', L('region') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $RegionModel = D('Region');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $RegionModel->m_edit($id, $data);
            if ($result_edit) {
                $this->success(L('region') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('region') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info                = $RegionModel->m_find($id);
        $edit_info['parent_name'] = $RegionModel->m_find_column($edit_info['parent_id'], 'region_name');
        $this->assign('edit_info', $edit_info);
        $this->assign('title', L('region') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $RegionModel = D('Region');
        $result_del  = $RegionModel->m_del($id);
        if ($result_del) {
            $this->success(L('region') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('region') . L('del') . L('error'), U('index'));
        }
    }

    //构造数据
    private function _make_data()
    {
        //初始化参数
        $parent_id   = I('parent_id');
        $region_name = I('region_name');
        $short_name  = I('short_name');
        $short_spell = I('short_spell');
        $areacode    = I('areacode');
        $postcode    = I('postcode');
        $if_show     = I('if_show');

        $data                                                                   = array();
        ('add' == ACTION_NAME || null !== $parent_id) && $data['parent_id']     = $parent_id;
        ('add' == ACTION_NAME || null !== $region_name) && $data['region_name'] = $region_name;
        ('add' == ACTION_NAME || null !== $short_name) && $data['short_name']   = $short_name;
        ('add' == ACTION_NAME || null !== $short_spell) && $data['short_spell'] = $short_spell;
        ('add' == ACTION_NAME || null !== $areacode) && $data['areacode']       = $areacode;
        ('add' == ACTION_NAME || null !== $postcode) && $data['postcode']       = $postcode;
        ('add' == ACTION_NAME || null !== $if_show) && $data['if_show']         = $if_show;
        return $data;
    }

}
