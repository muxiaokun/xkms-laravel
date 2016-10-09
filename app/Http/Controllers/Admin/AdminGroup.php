<?php
// 后台 管理员组

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class AdminGroup extends Backend
{
    //列表
    public function index()
    {
        $AdminGroupModel = D('AdminGroup');
        $where           = [];
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFindAllow  = $AdminGroupModel->mFind_allow();
            $where['id'] = ['in', $mFindAllow];
        }
        //建立where
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $adminGroupList = $AdminGroupModel->mSelect($where, true);
        $this->assign('admin_group_list', $adminGroupList);
        $this->assign('admin_group_list_count', $AdminGroupModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo              = [];
        $whereInfo['name']      = ['type' => 'input', 'name' => trans('group') . trans('name')];
        $whereInfo['is_enable'] = ['type'  => 'select',
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

        $this->assign('title', trans('admin') . trans('group') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        if (IS_POST) {
            $data      = $this->makeData();
            $resultAdd = $AdminGroupModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('management') . trans('group') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('management') . trans('group') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->addEditCommon();
        $this->assign('title', trans('admin') . trans('group') . trans('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = $AdminGroupModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('management') . trans('group') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('management') . trans('group') . trans('edit') . trans('error'), $errorGoLink);
            }
        }
        //获取分组默认信息
        $editInfo = $AdminGroupModel->mFind($id);
        foreach ($editInfo['manage_id'] as $manageKey => $manageId) {
            $adminName                         = $AdminModel->mFindColumn($manageId, 'admin_name');
            $editInfo['manage_id'][$manageKey] = ['value' => $manageId, 'html' => $adminName];
        }
        $editInfo['manage_id'] = json_encode($editInfo['manage_id']);
        $this->assign('edit_info', $editInfo);

        $this->addEditCommon();
        $this->assign('title', trans('admin') . trans('group') . trans('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $AdminGroupModel = D('AdminGroup');
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFindAllow = $AdminGroupModel->mFind_allow();
        }
        if ($id == 1 || (!in_array($id, $mFindAllow) && count(0 < $mFindAllow))) {
            $this->error(trans('id') . trans('not') . trans('del'), route('index'));
        }

        $resultDel = $AdminGroupModel->mDel($id);
        if ($resultDel) {
            //删除成功后 删除管理员与组的关系
            $AdminModel = D('Admin');
            $AdminModel->mClean($id, 'group_id');
            $this->success(trans('management') . trans('group') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('management') . trans('group') . trans('del') . trans('error'), route('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'name':
                //不能为空
                if ('' == $data['name']) {
                    $result['info'] = trans('admin') . trans('group') . trans('name') . trans('not') . trans('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('name_format_error', ['string' => $matches[1]]);
                    break;
                }
                //检查管理组名是否存在
                $AdminGroupModel = D('AdminGroup');
                $adminInfo       = $AdminGroupModel->mSelect(['name' => $data['name'], 'id' => ['neq', $data['id']]]);
                if (0 < count($adminInfo)) {
                    $result['info'] = trans('admin') . trans('group') . trans('name') . trans('exists');
                    break;
                }
                break;
            case 'privilege':
                //对比权限
                $privilege      = $this->getPrivilege('Admin', session('backend_info.privilege'));
                $checkPrivilege = [];
                foreach ($privilege as $controllerCn => $privs) {
                    foreach ($privs as $controllerName => $controller) {
                        foreach ($controller as $actionName => $action) {
                            $checkPrivilege[] = $controllerName . '_' . $actionName;

                        }
                    }
                }
                foreach ($data as $priv) {
                    if (!in_array($priv, $checkPrivilege)) {
                        $result['info'] = trans('privilege') . trans('submit') . trans('error');
                        break;
                    }
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'manage_id':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                $AdminModel = D('Admin');
                isset($data['keyword']) && $where['admin_name'] = ['like', '%' . $data['keyword'] . '%'];
                $adminUserList = $AdminModel->mSelect($where);
                foreach ($adminUserList as $adminUser) {
                    $result['info'][] = ['value' => $adminUser['id'], 'html' => $adminUser['admin_name']];
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $id       = request('id');
        $manageId = request('manage_id');
        $addId    = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manageId)
            && !in_array($addId, $manageId)
        ) {
            $manageId[] = $addId;
        }

        $name      = request('name');
        $explains  = request('explains');
        $privilege = request('privilege');
        $isEnable  = request('is_enable');

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', ['id' => $id]);
        if ('add' == ACTION_NAME || null !== $name) {
            $result = $this->doValidateForm('name', ['id' => $id, 'name' => $name]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $privilege) {
            $result = $this->doValidateForm('privilege', $privilege);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data = [];
        ('add' == ACTION_NAME || null !== $manageId) && $data['manage_id'] = $manageId;
        ('add' == ACTION_NAME || null !== $name) && $data['name'] = $name;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains'] = $explains;
        ('add' == ACTION_NAME || null !== $privilege) && $data['privilege'] = $privilege;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;
        return $data;
    }

    //构造管理组assign公共数据
    private function addEditCommon()
    {
        $this->assign('privilege', $this->getPrivilege('Admin', session('backend_info.privilege')));
    }
}
