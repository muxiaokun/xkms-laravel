<?php
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
        $whereValue                          = '';
        $whereValue                          = I('region_name');
        $whereValue && $where['region_name'] = array('like', '%' . $whereValue . '%');
        $whereValue                          = I('short_spell');
        $whereValue && $where['short_spell'] = array('like', '%' . $whereValue . '%');
        $whereValue                          = I('areacode');
        $whereValue && $where['areacode']    = array('like', '%' . $whereValue . '%');
        $whereValue                          = I('postcode');
        $whereValue && $where['postcode']    = array('like', '%' . $whereValue . '%');

        //初始化翻页 和 列表数据
        $regionList = $RegionModel->mSelect($where, true);
        foreach ($regionList as &$region) {
            $region['parent_name'] = $RegionModel->mFindColumn($region['parent_id'], 'region_name');
        }

        $this->assign('region_list', $regionList);
        $this->assign('region_list_count', $RegionModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo['region_name'] = array('type' => 'input', 'name' => L('region_name'));
        $whereInfo['short_spell'] = array('type' => 'input', 'name' => L('short_spell'));
        $whereInfo['areacode']    = array('type' => 'input', 'name' => L('areacode'));
        $whereInfo['postcode']    = array('type' => 'input', 'name' => L('postcode'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('region') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $RegionModel = D('Region');
            $data        = $this->makeData();
            $resultAdd  = $RegionModel->mAdd($data);
            if ($resultAdd) {
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
            $data        = $this->makeData();
            $resultEdit = $RegionModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(L('region') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('region') . L('edit') . L('error'), $errorGoLink);
            }
        }

        $editInfo                = $RegionModel->mFind($id);
        $editInfo['parent_name'] = $RegionModel->mFindColumn($editInfo['parent_id'], 'region_name');
        $this->assign('edit_info', $editInfo);
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
        $resultDel  = $RegionModel->mDel($id);
        if ($resultDel) {
            $this->success(L('region') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('region') . L('del') . L('error'), U('index'));
        }
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $parentId   = I('parent_id');
        $regionName = I('region_name');
        $shortName  = I('short_name');
        $shortSpell = I('short_spell');
        $areacode    = I('areacode');
        $postcode    = I('postcode');
        $ifShow     = I('if_show');

        $data                                                                   = array();
        ('add' == ACTION_NAME || null !== $parentId) && $data['parent_id']     = $parentId;
        ('add' == ACTION_NAME || null !== $regionName) && $data['region_name'] = $regionName;
        ('add' == ACTION_NAME || null !== $shortName) && $data['short_name']   = $shortName;
        ('add' == ACTION_NAME || null !== $shortSpell) && $data['short_spell'] = $shortSpell;
        ('add' == ACTION_NAME || null !== $areacode) && $data['areacode']       = $areacode;
        ('add' == ACTION_NAME || null !== $postcode) && $data['postcode']       = $postcode;
        ('add' == ACTION_NAME || null !== $ifShow) && $data['if_show']         = $ifShow;
        return $data;
    }

}
