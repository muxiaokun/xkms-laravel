<?php
//后台 地域

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Region extends Backend
{
    //列表
    public function index()
    {
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
        $regionList = Model\Region::mSelect($where, true);
        foreach ($regionList as &$region) {
            $region['parent_name'] = Model\Region::mFindColumn($region['parent_id'], 'region_name');
        }

        $assign['region_list']       = $regionList;
        $assign['region_list_count'] = Model\Region::mGetPageCount($where);

        //初始化where_info
        $whereInfo['region_name'] = ['type' => 'input', 'name' => trans('common.region_name')];
        $whereInfo['short_spell'] = ['type' => 'input', 'name' => trans('common.short_spell')];
        $whereInfo['areacode']    = ['type' => 'input', 'name' => trans('common.areacode')];
        $whereInfo['postcode']    = ['type' => 'input', 'name' => trans('common.postcode')];
        $assign['where_info']     = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.region') . trans('common.management');
        return view('admin.', $assign);
    }

    //新增
    public function add()
    {
        if ('POST' == request()->getMethod()) {
            $data      = $this->makeData();
            $resultAdd = Model\Region::mAdd($data);
            if ($resultAdd) {
                $this->success(trans('common.region') . trans('common.add') . trans('common.success'), route('index'));
                return;
            } else {
                $this->error(trans('common.region') . trans('common.add') . trans('common.error'), route('add'));
            }
        }

        $assign['title'] = trans('common.region') . trans('common.add');
        return view('admin.addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if ('POST' == request()->getMethod()) {
            $data       = $this->makeData();
            $resultEdit = Model\Region::mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('common.region') . trans('common.edit') . trans('common.success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('common.region') . trans('common.edit') . trans('common.error'), $errorGoLink);
            }
        }

        $editInfo                = Model\Region::mFind($id);
        $editInfo['parent_name'] = Model\Region::mFindColumn($editInfo['parent_id'], 'region_name');
        $assign['edit_info']     = $editInfo;
        $assign['title']         = trans('common.region') . trans('common.edit');
        return view('admin.addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\Region::mDel($id);
        if ($resultDel) {
            $this->success(trans('common.region') . trans('common.del') . trans('common.success'), route('index'));
            return;
        } else {
            $this->error(trans('common.region') . trans('common.del') . trans('common.error'), route('index'));
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
