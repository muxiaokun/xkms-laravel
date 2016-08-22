<?php
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Navigation extends Backend
{
    //导航级别 post_name
    private $navigationConfig = array('navigation_level' => 4, 'post_name' => 'navigation_list');

    //列表
    public function index()
    {
        $NavigationModel = D('Navigation');
        //建立where
        $whereValue                         = '';
        $whereValue                         = I('name');
        $whereValue && $where['name']       = array('like', '%' . $whereValue . '%');
        $whereValue                         = I('short_name');
        $whereValue && $where['short_name'] = array('like', '%' . $whereValue . '%');
        $whereValue                         = I('is_enable');
        $whereValue && $where['is_enable']  = (1 == $whereValue) ? 1 : 0;
        //初始化翻页 和 列表数据
        $navigationList = $NavigationModel->mSelect($where, true);
        $this->assign('navigation_list', $navigationList);
        $this->assign('navigation_list_count', $NavigationModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = array();
        $whereInfo['name']       = array('type' => 'input', 'name' => L('navigation') . L('name'));
        $whereInfo['short_name'] = array('type' => 'input', 'name' => L('short') . L('name'));
        $whereInfo['is_enable']  = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('enable'), 2 => L('disable')));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('navigation') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $NavigationModel = D('Navigation');
            $data            = $this->makeData();
            $resultAdd      = $NavigationModel->mAdd($data);
            if ($resultAdd) {
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
            $data        = $this->makeData();
            $resultEdit = $NavigationModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(L('navigation') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('navigation') . L('edit') . L('error'), $errorGoLink);
            }
        }

        $editInfo = $NavigationModel->mFind($id);
        //$editInfo['ext_info'] = json_encode($editInfo['ext_info']);
        $this->assign('edit_info', $editInfo);

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
        $resultDel      = $NavigationModel->mDel($id);
        if ($resultDel) {
            $this->success(L('navigation') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('navigation') . L('del') . L('error'), U('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $NavigationModel = D('Navigation');
                $itlinkInfo     = $NavigationModel->mSelect(array('short_name' => $data['short_name'], 'id' => array('neq', $data['id'])));
                if (0 < count($itlinkInfo)) {
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
    private function makeData()
    {
        //初始化参数
        $id         = I('id');
        $name       = I('name');
        $shortName = I('short_name');
        $isEnable  = I('is_enable');
        $extInfo   = $this->_make_navigation(I($this->navigation_config['post_name']));

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $shortName) {
            $result = $this->doValidateForm('short_name', array('id' => $id, 'short_name' => $shortName));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data                                                                 = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']             = $name;
        ('add' == ACTION_NAME || null !== $shortName) && $data['short_name'] = $shortName;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable']   = $isEnable;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info']     = $extInfo;

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
