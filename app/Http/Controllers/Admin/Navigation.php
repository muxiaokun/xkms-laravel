<?php
// 后台 导航

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Navigation extends Backend
{
    //导航级别 post_name
    private $navigationConfig = ['navigation_level' => 4, 'post_name' => 'navigation_list'];

    //列表
    public function index()
    {
        $NavigationModel = D('Navigation');
        //建立where
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('short_name');
        $whereValue && $where['short_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;
        //初始化翻页 和 列表数据
        $navigationList = $NavigationModel->mSelect($where, true);
        $this->assign('navigation_list', $navigationList);
        $this->assign('navigation_list_count', $NavigationModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['name']       = ['type' => 'input', 'name' => trans('navigation') . trans('name')];
        $whereInfo['short_name'] = ['type' => 'input', 'name' => trans('short') . trans('name')];
        $whereInfo['is_enable']  = [
            'type'  => 'select',
            'name'  => trans('yes') . trans('no') . trans('enable'),
            'value' => [1 => trans('enable'), 2 => trans('disable')],
        ];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = [];
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('navigation') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $NavigationModel = D('Navigation');
            $data            = $this->makeData();
            $resultAdd       = $NavigationModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('navigation') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('navigation') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->assign('navigation_config', $this->navigation_config);
        $this->assign('title', trans('add') . trans('navigation'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $NavigationModel = D('Navigation');
        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = $NavigationModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('navigation') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('navigation') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo = $NavigationModel->mFind($id);
        //$editInfo['ext_info'] = json_encode($editInfo['ext_info']);
        $this->assign('edit_info', $editInfo);

        $this->assign('navigation_config', $this->navigation_config);
        $this->assign('title', trans('edit') . trans('navigation'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $NavigationModel = D('Navigation');
        $resultDel       = $NavigationModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('navigation') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('navigation') . trans('del') . trans('error'), route('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $NavigationModel = D('Navigation');
                $itlinkInfo      = $NavigationModel->mSelect([
                    'short_name' => $data['short_name'],
                    'id'         => ['neq', $data['id']],
                ]);
                if (0 < count($itlinkInfo)) {
                    $result['info'] = trans('short') . trans('name') . trans('exists');
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
                $this->error($result['info'], $errorGoLink);
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
