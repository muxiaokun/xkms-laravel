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
        $whereValue = '';
        $whereValue = request('region_name');
        $whereValue && $where['region_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('short_spell');
        $whereValue && $where['short_spell'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('areacode');
        $whereValue && $where['areacode'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('postcode');
        $whereValue && $where['postcode'] = ['like', '%' . $whereValue . '%'];

        //初始化翻页 和 列表数据
        $regionList = $RegionModel->mSelect($where, true);
        foreach ($regionList as &$region) {
            $region['parent_name'] = $RegionModel->mFindColumn($region['parent_id'], 'region_name');
        }

        $this->assign('region_list', $regionList);
        $this->assign('region_list_count', $RegionModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo['region_name'] = ['type' => 'input', 'name' => trans('region_name')];
        $whereInfo['short_spell'] = ['type' => 'input', 'name' => trans('short_spell')];
        $whereInfo['areacode']    = ['type' => 'input', 'name' => trans('areacode')];
        $whereInfo['postcode']    = ['type' => 'input', 'name' => trans('postcode')];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('region') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $RegionModel = D('Region');
            $data        = $this->makeData();
            $resultAdd   = $RegionModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('region') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('region') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->assign('title', trans('region') . trans('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $RegionModel = D('Region');
        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = $RegionModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('region') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('region') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo                = $RegionModel->mFind($id);
        $editInfo['parent_name'] = $RegionModel->mFindColumn($editInfo['parent_id'], 'region_name');
        $this->assign('edit_info', $editInfo);
        $this->assign('title', trans('region') . trans('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $RegionModel = D('Region');
        $resultDel   = $RegionModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('region') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('region') . trans('del') . trans('error'), route('index'));
        }
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $parentId   = request('parent_id');
        $regionName = request('region_name');
        $shortName  = request('short_name');
        $shortSpell = request('short_spell');
        $areacode   = request('areacode');
        $postcode   = request('postcode');
        $ifShow     = request('if_show');

        $data = [];
        ('add' == ACTION_NAME || null !== $parentId) && $data['parent_id'] = $parentId;
        ('add' == ACTION_NAME || null !== $regionName) && $data['region_name'] = $regionName;
        ('add' == ACTION_NAME || null !== $shortName) && $data['short_name'] = $shortName;
        ('add' == ACTION_NAME || null !== $shortSpell) && $data['short_spell'] = $shortSpell;
        ('add' == ACTION_NAME || null !== $areacode) && $data['areacode'] = $areacode;
        ('add' == ACTION_NAME || null !== $postcode) && $data['postcode'] = $postcode;
        ('add' == ACTION_NAME || null !== $ifShow) && $data['if_show'] = $ifShow;
        return $data;
    }

}
