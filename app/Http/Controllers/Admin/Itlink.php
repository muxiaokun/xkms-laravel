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
// 后台 图文管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Itlink extends Backend
{
    //列表
    public function index()
    {
        $ItlinkModel = D('Itlink');
        //建立where
        $v_value                            = '';
        $v_value                            = I('name');
        $v_value && $where['name']          = array('like', '%' . $v_value . '%');
        $v_value                            = I('short_name');
        $v_value && $where['short_name']    = array('like', '%' . $v_value . '%');
        $v_value                            = I('is_enable');
        $v_value && $where['is_enable']     = (1 == $v_value) ? 1 : 0;
        $v_value                            = I('is_statistics');
        $v_value && $where['is_statistics'] = (1 == $v_value) ? 1 : 0;

        //初始化翻页 和 列表数据
        $itlink_list = $ItlinkModel->mSelect($where, true);
        $this->assign('itlink_list', $itlink_list);
        $this->assign('itlink_list_count', $ItlinkModel->getPageCount($where));

        //初始化where_info
        $where_info['name']          = array('type' => 'input', 'name' => L('itlink') . L('name'));
        $where_info['short_name']    = array('type' => 'input', 'name' => L('short') . L('name'));
        $where_info['is_enable']     = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('yes'), 2 => L('no')));
        $where_info['is_statistics'] = array('type' => 'select', 'name' => L('yes') . L('no') . L('statistics'), 'value' => array(1 => L('yes'), 2 => L('no')));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['add']  = $this->_check_privilege('add');
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('itlink') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ItlinkModel = D('Itlink');
            $data        = $this->_make_data();
            $result_add  = $ItlinkModel->mAdd($data);
            if ($result_add) {
                $this->_add_edit_after_common($data, $id);
                $this->success(L('itlink') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('itlink') . L('add') . L('error'), U('add'));
            }
        }

        $this->assign('title', L('itlink') . L('add'));
        $this->display('addedit');
    }
    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ItlinkModel = D('Itlink');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $ItlinkModel->mEdit($id, $data);
            if ($result_edit) {
                $this->_add_edit_after_common($data, $id);
                $this->success(L('itlink') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('itlink') . L('edit') . L('error'), $error_go_link);
            }
        }

        $edit_info = $ItlinkModel->mFind($id);
        $this->assign('edit_info', $edit_info);
        $this->assign('title', L('itlink') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ItlinkModel = D('Itlink');
        $result_del  = $ItlinkModel->mDel($id);
        if ($result_del) {
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->mEdit($id);
            $this->success(L('itlink') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('itlink') . L('del') . L('error'), U('index'));
        }
    }

    //异步和表单数据验证
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $ItlinkModel = D('Itlink');
                $itlink_info = $ItlinkModel->mSelect(array('short_name' => $data['short_name'], 'id' => array('neq', $data['id'])));
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
        $id             = I('id');
        $name           = I('name');
        $short_name     = I('short_name');
        $start_time     = I('start_time');
        $end_time       = I('end_time');
        $start_time     = M_mktime($start_time, true);
        $end_time       = M_mktime($end_time, true);
        $is_enable      = I('is_enable');
        $is_statistics  = I('is_statistics');
        $max_show_num   = I('max_show_num');
        $max_hit_num    = I('max_hit_num');
        $show_num       = I('show_num');
        $hit_num        = I('hit_num');
        $input_ext_info = I('ext_info');
        $ext_info       = array();
        foreach ($input_ext_info['itl_link'] as $info_key => $info) {
            $ext_info[] = array(
                'itl_link'   => htmlspecialchars_decode($info),
                'itl_text'   => htmlspecialchars_decode($input_ext_info['itl_text'][$info_key]),
                'itl_target' => htmlspecialchars_decode($input_ext_info['itl_target'][$info_key]),
                'itl_image'  => htmlspecialchars_decode($input_ext_info['itl_image'][$info_key]),
            );
        }

        //检测初始化参数是否合法
        $error_go_link = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $short_name) {
            $result = $this->_validform('short_name', array('id' => $id, 'short_name' => $short_name));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }

        $data                                                                       = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']                   = $name;
        ('add' == ACTION_NAME || null !== $short_name) && $data['short_name']       = $short_name;
        ('add' == ACTION_NAME || null !== $start_time) && $data['start_time']       = $start_time;
        ('add' == ACTION_NAME || null !== $end_time) && $data['end_time']           = $end_time;
        ('add' == ACTION_NAME || null !== $is_enable) && $data['is_enable']         = $is_enable;
        ('add' == ACTION_NAME || null !== $is_statistics) && $data['is_statistics'] = $is_statistics;
        ('add' == ACTION_NAME || null !== $max_show_num) && $data['max_show_num']   = $max_show_num;
        ('add' == ACTION_NAME || null !== $max_hit_num) && $data['max_hit_num']     = $max_hit_num;
        ('add' == ACTION_NAME || null !== $show_num) && $data['show_num']           = $show_num;
        ('add' == ACTION_NAME || null !== $hit_num) && $data['hit_num']             = $hit_num;
        ('add' == ACTION_NAME || 0 < count($ext_info)) && $data['ext_info']         = $ext_info;
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function _add_edit_after_common(&$data, $id)
    {
        // 批量修改时不进行文件绑定
        if (is_array($id)) {
            return;
        }

        $ManageUploadModel = D('ManageUpload');
        $bind_file         = array();
        foreach ($data as $item) {
            $bind_file[] = $item['link_image'];
        }
        $ManageUploadModel->mEdit($id, $bind_file);
    }
}
