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
        //初始化翻页 和 列表数据
        $regionList = Model\Region::where(function ($query) {
            $regionName = request('region_name');
            if ($regionName) {
                $query->where('region_name', 'like', '%' . $regionName . '%');
            }

            $shortSpell = request('short_spell');
            if ($shortSpell) {
                $query->where('short_spell', 'like', '%' . $shortSpell . '%');
            }

            $areacode = request('areacode');
            if ($areacode) {
                $query->where('areacode', 'like', '%' . $areacode . '%');
            }

            $postcode = request('postcode');
            if ($postcode) {
                $query->where('postcode', 'like', '%' . $postcode . '%');
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
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
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Region::create($data);
            if ($resultAdd) {
                return $this->success(trans('region.region') . trans('common.add') . trans('common.success'),
                    route('Admin::Region::index'));
            } else {
                return $this->error(trans('region.region') . trans('common.add') . trans('common.error'),
                    route('Admin::Region::add'));
            }
        }

        $assign['edit_info'] = Model\Region::columnEmptyData();
        $assign['title'] = trans('region.region') . trans('common.add');
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
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = false;
            Model\Region::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('region.region') . trans('common.edit') . trans('common.success'),
                    route('Admin::Region::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Region::index') : route('Admin::Region::edit',
                    ['id' => $id]);
                return $this->error(trans('region.region') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo                = Model\Region::colWhere($id)->first()->toArray();
        $editInfo['parent_name'] = Model\Region::colWhere($editInfo['parent_id'])->first()['region_name'];
        $assign['edit_info']     = $editInfo;
        $assign['title'] = trans('region.region') . trans('common.edit');
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
            return $this->success(trans('region.region') . trans('common.del') . trans('common.success'),
                route('Admin::Region::index'));
        } else {
            return $this->error(trans('region.region') . trans('common.del') . trans('common.error'),
                route('Admin::Region::index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'parent_id':
                Model\Region::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('region_name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->take(10)->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['region_name']];
                });
                break;
        }

        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $parentId   = request('parent_id');
        $regionName = request('region_name');
        $shortName  = request('short_name');
        $allSpell = request('all_spell');
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
        if ('add' == $type || null !== $allSpell) {
            $data['all_spell'] = $allSpell;
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
