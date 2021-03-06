<?php
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\Validator;

class Navigation extends Backend
{
    //导航级别 post_name
    private $navigationConfig = ['navigation_level' => 4, 'post_name' => 'navigation_list'];

    //列表
    public function index()
    {
        //初始化翻页 和 列表数据
        $navigationList            = Model\Navigation::where(function ($query) {
            $name = request('name');
            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            $shortName = request('short_name');
            if ($shortName) {
                $query->where('short_name', 'like', '%' . $shortName . '%');
            }

            $is_enable = request('is_enable');
            if ($is_enable) {
                $query->where('is_enable', '=', (1 == $is_enable) ? 1 : 0);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['navigation_list'] = $navigationList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['name']       = ['type' => 'input', 'name' => trans('common.navigation') . trans('common.name')];
        $whereInfo['short_name'] = ['type' => 'input', 'name' => trans('common.short') . trans('common.name')];
        $whereInfo['is_enable']  = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
            'value' => [1 => trans('common.enable'), 2 => trans('common.disable')],
        ];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('Admin::Navigation::add');
        $batchHandle['edit']    = $this->_check_privilege('Admin::Navigation::edit');
        $batchHandle['del']     = $this->_check_privilege('Admin::Navigation::del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.navigation') . trans('common.management');
        return view('admin.Navigation_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Navigation::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.navigation') . trans('common.add') . trans('common.success'),
                    route('Admin::Navigation::index'));
            } else {
                return $this->error(trans('common.navigation') . trans('common.add') . trans('common.error'),
                    route('Admin::Navigation::add'));
            }
        }

        $assign['navigation_config'] = $this->navigationConfig;
        $assign['edit_info']         = Model\Navigation::columnEmptyData();
        $assign['title']             = trans('common.add') . trans('common.navigation');
        return view('admin.Navigation_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Navigation::index'));
        }

        if (request()->isMethod('POST')) {
            $data = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = false;
            Model\Navigation::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('common.navigation') . trans('common.edit') . trans('common.success'),
                    route('Admin::Navigation::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Navigation::index') : route('Admin::Navigation::edit',
                    ['id' => $id]);
                return $this->error(trans('common.navigation') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo            = Model\Navigation::colWhere($id)->first()->toArray();
        $assign['edit_info'] = $editInfo;

        $assign['navigation_config'] = $this->navigationConfig;
        $assign['title']             = trans('common.edit') . trans('common.navigation');
        return view('admin.Navigation_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Navigation::index'));
        }

        $resultDel = Model\Navigation::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.navigation') . trans('common.del') . trans('common.success'),
                route('Admin::Navigation::index'));
        } else {
            return $this->error(trans('common.navigation') . trans('common.del') . trans('common.error'),
                route('Admin::Navigation::index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'short_name':
                $validator = Validator::make($data, [
                    'short_name' => 'short_name|navigation_name_exist',
                ]);
                break;
        }

        if (isset($validator) && $validator->fails()) {
            $result['info'] = implode('', $validator->errors()->all());
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $id        = request('id');
        $name      = request('name');
        $shortName = request('short_name');
        $isEnable  = request('is_enable');
        $extInfo = $this->_make_navigation(request($this->navigationConfig['post_name']));

        //检测初始化参数是否合法
        if ($id) {
            if (is_array($id)) {
                $errorGoLink = route('Admin::Navigation::index');
            } else {
                $errorGoLink = route('Admin::Navigation::edit', ['id' => $id]);
            }
        } else {
            $errorGoLink = route('Admin::Navigation::add');
        }

        $data = [];
        if ('add' == $type || null !== $name) {
            $data['name'] = $name;
        }
        if ('add' == $type || null !== $shortName) {
            $result = $this->doValidateForm('short_name', ['id' => $id, 'short_name' => $shortName]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['short_name'] = $shortName;
        }
        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;
        }
        if ('add' == $type || null !== $extInfo) {
            $data['ext_info'] = $extInfo;
        }

        return $data;
    }

    //构造导航数据
    private function _make_navigation($data, $pid = 0)
    {
        $result = [];
        if (isset($data[$pid]) && is_array($data[$pid])) {
            foreach ($data[$pid] as $nav) {
                $child              = json_decode(str_replace(['&quot;', '&amp;'], ['"', '&'], $nav), true);
                $child['nav_child'] = $this->_make_navigation($data, $child['nav_id']);
                $result[]           = $child;
            }
        }
        return $result;
    }
}
