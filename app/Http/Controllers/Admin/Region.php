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
        $where      = [];
        $whereValue = request('region_name');
        $whereValue && $where['region_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('short_spell');
        $whereValue && $where['short_spell'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('areacode');
        $whereValue && $where['areacode'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('postcode');
        $whereValue && $where['postcode'] = ['like', '%' . $whereValue . '%'];

        //初始化翻页 和 列表数据
        $regionList = Model\Region::where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($regionList as &$region) {
            $region['parent_name'] = Model\Region::colWhere($region['parent_id'])->first()['region_name'];
        }

        $assign['region_list'] = $regionList;

        //初始化where_info
        $whereInfo['region_name'] = ['type' => 'input', 'name' => trans('region.region_name')];
        $whereInfo['short_spell'] = ['type' => 'input', 'name' => trans('region.short_spell')];
        $whereInfo['areacode']    = ['type' => 'input', 'name' => trans('region.areacode')];
        $whereInfo['postcode']    = ['type' => 'input', 'name' => trans('region.postcode')];
        $assign['where_info']     = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('region.region') . trans('common.management');
        return view('admin.Region_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            $resultAdd = Model\Region::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.region') . trans('common.add') . trans('common.success'),
                    route('Admin::Region::index'));
            } else {
                return $this->error(trans('common.region') . trans('common.add') . trans('common.error'),
                    route('Admin::Region::add'));
            }
        }

        $assign['edit_info'] = Model\Region::columnEmptyData();
        $assign['title']     = trans('common.region') . trans('common.add');
        return view('admin.Region_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Region::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData('edit');
            $resultEdit = Model\Region::colWhere($id)->first()->update($data);
            if ($resultEdit) {
                return $this->success(trans('common.region') . trans('common.edit') . trans('common.success'),
                    route('Admin::Region::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Region::index') : route('Admin::Region::edit',
                    ['id' => $id]);
                return $this->error(trans('common.region') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo                = Model\Region::colWhere($id)->first()->toArray();
        $editInfo['parent_name'] = Model\Region::colWhere($editInfo['parent_id'])->first()['region_name'];
        $assign['edit_info']     = $editInfo;
        $assign['title']         = trans('common.region') . trans('common.edit');
        return view('admin.Region_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Region::index'));
        }

        $resultDel = Model\Region::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.region') . trans('common.del') . trans('common.success'),
                route('Admin::Region::index'));
        } else {
            return $this->error(trans('common.region') . trans('common.del') . trans('common.error'),
                route('Admin::Region::index'));
        }
    }

    //构造数据
    private function makeData($type)
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
        if ('add' == $type || null !== $parentId) {
            $data['parent_id'] = $parentId;
        }
        if ('add' == $type || null !== $regionName) {
            $data['region_name'] = $regionName;
        }
        if ('add' == $type || null !== $shortName) {
            $data['short_name'] = $shortName;
        }
        if ('add' == $type || null !== $shortSpell) {
            $data['short_spell'] = $shortSpell;
        }
        if ('add' == $type || null !== $areacode) {
            $data['areacode'] = $areacode;
        }
        if ('add' == $type || null !== $postcode) {
            $data['postcode'] = $postcode;
        }
        if ('add' == $type || null !== $ifShow) {
            $data['if_show'] = $ifShow;
        }
        return $data;
    }

}
