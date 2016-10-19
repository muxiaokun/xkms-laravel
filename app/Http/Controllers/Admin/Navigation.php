<?php
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Navigation extends Backend
{
    //导航级别 post_name
    private $navigationConfig = ['navigation_level' => 4, 'post_name' => 'navigation_list'];

    //列表
    public function index()
    {
        //建立where
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('short_name');
        $whereValue && $where['short_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;
        //初始化翻页 和 列表数据
        $navigationList                  = Model\Navigation::mSelect($where, true);
        $assign['navigation_list']       = $navigationList;
        $assign['navigation_list_count'] = Model\Navigation::mGetPageCount($where);

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
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.navigation') . trans('common.management');
        return view('admin.', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData();
            $resultAdd = Model\Navigation::mAdd($data);
            if ($resultAdd) {
                return $this->success(trans('common.navigation') . trans('common.add') . trans('common.success'),
                    route('index'));
                return;
            } else {
                return $this->error(trans('common.navigation') . trans('common.add') . trans('common.error'),
                    route('add'));
            }
        }

        $assign['navigation_config'] = $this->navigation_config;
        $assign['title']             = trans('common.add') . trans('common.navigation');
        return view('admin.addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData();
            $resultEdit = Model\Navigation::mEdit($id, $data);
            if ($resultEdit) {
                return $this->success(trans('common.navigation') . trans('common.edit') . trans('common.success'),
                    route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                return $this->error(trans('common.navigation') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo = Model\Navigation::mFind($id);
        //$editInfo['ext_info'] = json_encode($editInfo['ext_info']);
        $assign['edit_info'] = $editInfo;

        $assign['navigation_config'] = $this->navigation_config;
        $assign['title']             = trans('common.edit') . trans('common.navigation');
        return view('admin.addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\Navigation::mDel($id);
        if ($resultDel) {
            return $this->success(trans('common.navigation') . trans('common.del') . trans('common.success'),
                route('index'));
            return;
        } else {
            return $this->error(trans('common.navigation') . trans('common.del') . trans('common.error'),
                route('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $itlinkInfo = Model\Navigation::mSelect([
                    'short_name' => $data['short_name'],
                    'id'         => ['neq', $data['id']],
                ]);
                if (0 < count($itlinkInfo)) {
                    $result['info'] = trans('common.short') . trans('common.name') . trans('common.exists');
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
        $id        = request('id');
        $name      = request('name');
        $shortName = request('short_name');
        $isEnable  = request('is_enable');
        $extInfo   = $this->_make_navigation(request($this->navigation_config['post_name']));

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', ['id' => $id]);
        if ('add' == ACTION_NAME || null !== $shortName) {
            $result = $this->doValidateForm('short_name', ['id' => $id, 'short_name' => $shortName]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }

        }

        $data = [];
        ('add' == ACTION_NAME || null !== $name) && $data['name'] = $name;
        ('add' == ACTION_NAME || null !== $shortName) && $data['short_name'] = $shortName;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info'] = $extInfo;

        return $data;
    }

    //构造导航数据
    private function _make_navigation(&$data, $pid = 0)
    {
        $result = [];
        foreach ($data[$pid] as $nav) {
            $child              = json_decode(str_replace(['&quot;', '&amp;'], ['"', '&'], $nav), true);
            $child['nav_child'] = $this->_make_navigation($data, $child['nav_id']);
            $result[]           = $child;
        }
        return json_encode($result);
    }
}
