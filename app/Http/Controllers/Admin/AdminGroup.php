<?php
// +----------------------------------------------------------------------
// | Core : ThinkPHP Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | APP  : Copyright (c) 2014-ALL http://wumingmxk.xicp.net rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: merry M  <test20121212@qq.com>
// +----------------------------------------------------------------------
// 后台 管理员组

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class AdminGroup extends Backend
{
    //列表
    public function index()
    {
        $AdminGroupModel = D('AdminGroup');
        $where           = array();
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFindAllow = $AdminGroupModel->mFind_allow();
            $where['id']  = array('in', $mFindAllow);
        }
        //建立where
        $whereValue                        = '';
        $whereValue                        = I('name');
        $whereValue && $where['name']      = array('like', '%' . $whereValue . '%');
        $whereValue                        = I('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $adminGroupList = $AdminGroupModel->mSelect($where, true);
        $this->assign('admin_group_list', $adminGroupList);
        $this->assign('admin_group_list_count', $AdminGroupModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo              = array();
        $whereInfo['name']      = array('type' => 'input', 'name' => L('group') . L('name'));
        $whereInfo['is_enable'] = array('type' => 'select', 'name' => L('yes') . L('no') . L('enable'), 'value' => array(1 => L('enable'), 2 => L('disable')));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('admin') . L('group') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        if (IS_POST) {
            $data       = $this->makeData();
            $resultAdd = $AdminGroupModel->mAdd($data);
            if ($resultAdd) {
                $this->success(L('management') . L('group') . L('add') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('management') . L('group') . L('add') . L('error'), U('add'));
            }
        }

        $this->addEditCommon();
        $this->assign('title', L('admin') . L('group') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminModel      = D('Admin');
        $AdminGroupModel = D('AdminGroup');
        if (IS_POST) {
            $data        = $this->makeData();
            $resultEdit = $AdminGroupModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(L('management') . L('group') . L('edit') . L('success'), U('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('management') . L('group') . L('edit') . L('error'), $errorGoLink);
            }
        }
        //获取分组默认信息
        $editInfo = $AdminGroupModel->mFind($id);
        foreach ($editInfo['manage_id'] as $manageKey => $manageId) {
            $adminName                          = $AdminModel->mFindColumn($manageId, 'admin_name');
            $editInfo['manage_id'][$manageKey] = array('value' => $manageId, 'html' => $adminName);
        }
        $editInfo['manage_id'] = json_encode($editInfo['manage_id']);
        $this->assign('edit_info', $editInfo);

        $this->addEditCommon();
        $this->assign('title', L('admin') . L('group') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AdminGroupModel = D('AdminGroup');
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFindAllow = $AdminGroupModel->mFind_allow();
        }
        if ($id == 1 || (!in_array($id, $mFindAllow) && count(0 < $mFindAllow))) {
            $this->error(L('id') . L('not') . L('del'), U('index'));
        }

        $resultDel = $AdminGroupModel->mDel($id);
        if ($resultDel) {
            //删除成功后 删除管理员与组的关系
            $AdminModel = D('Admin');
            $AdminModel->mClean($id, 'group_id');
            $this->success(L('management') . L('group') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('management') . L('group') . L('del') . L('error'), U('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'name':
                //不能为空
                if ('' == $data['name']) {
                    $result['info'] = L('admin') . L('group') . L('name') . L('not') . L('empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != C('DEFAULT_CHARSET')) {
                    $data['name'] = iconv(C('DEFAULT_CHARSET'), 'utf-8', $data['name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = L('name_format_error', array('string' => $matches[1]));
                    break;
                }
                //检查管理组名是否存在
                $AdminGroupModel = D('AdminGroup');
                $adminInfo      = $AdminGroupModel->mSelect(array('name' => $data['name'], 'id' => array('neq', $data['id'])));
                if (0 < count($adminInfo)) {
                    $result['info'] = L('admin') . L('group') . L('name') . L('exists');
                    break;
                }
                break;
            case 'privilege':
                //对比权限
                $privilege       = $this->getPrivilege('Admin', session('backend_info.privilege'));
                $checkPrivilege = array();
                foreach ($privilege as $controllerCn => $privs) {
                    foreach ($privs as $controllerName => $controller) {
                        foreach ($controller as $actionName => $action) {
                            $checkPrivilege[] = $controllerName . '_' . $actionName;

                        }
                    }
                }
                foreach ($data as $priv) {
                    if (!in_array($priv, $checkPrivilege)) {
                        $result['info'] = L('privilege') . L('submit') . L('error');
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
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'manage_id':
                isset($data['inserted']) && $where['id']        = array('not in', $data['inserted']);
                $AdminModel                                     = D('Admin');
                isset($data['keyword']) && $where['admin_name'] = array('like', '%' . $data['keyword'] . '%');
                $adminUserList                                = $AdminModel->mSelect($where);
                foreach ($adminUserList as $adminUser) {
                    $result['info'][] = array('value' => $adminUser['id'], 'html' => $adminUser['admin_name']);
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $id        = I('id');
        $manageId = I('manage_id');
        $addId    = session('backend_info.id');
        if (('add' == ACTION_NAME || null !== $manageId)
            && !in_array($addId, $manageId)
        ) {
            $manageId[] = $addId;
        }

        $name      = I('name');
        $explains  = I('explains');
        $privilege = I('privilege');
        $isEnable = I('is_enable');

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $name) {
            $result = $this->doValidateForm('name', array('id' => $id, 'name' => $name));
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

        $data                                                               = array();
        ('add' == ACTION_NAME || null !== $manageId) && $data['manage_id'] = $manageId;
        ('add' == ACTION_NAME || null !== $name) && $data['name']           = $name;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains']   = $explains;
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
