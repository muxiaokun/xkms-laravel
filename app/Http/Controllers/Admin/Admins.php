<?php
// 后台 管理员

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Admin extends Backend
{
    //列表
    public function index()
    {
        $where           = [];
        if (1 != session('backend_info.id')) {
            //非root需要权限
            $mFindAllow        = Model\AdminGroup::mFind_allow();
            $where['group_id'] = $mFindAllow;
        }
        //建立where
        $whereValue = '';
        $whereValue = request('admin_name');
        $whereValue && $where['admin_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('group_id');
        $whereValue && $where['group_id'] = Model\AdminGroup::where([
            'name' => [
                'like',
                '%' . $whereValue . '%',
            ],
        ])->mColumn2Array('id');
        $whereValue = mMktimeRange('last_time');
        $whereValue && $where['last_time'] = $whereValue;
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $adminList = Model\Admins::mSelect($where, true);
        foreach ($adminList as &$admin) {
            foreach ($admin['group_id'] as $groupId) {
                $groupName = Model\AdminGroup::mFindColumn($groupId, 'name');
                isset($admin['group_name']) && $admin['group_name'] .= " | ";
                $admin['group_name'] .= $groupName;
            }
            !isset($admin['group_name']) && $admin['group_name'] = trans('common.empty');
            !isset($admin['add_time']) && $admin['add_time'] = trans('common.system') . trans('common.add');
        }
        $assign['admin_list']       = $adminList;
        $assign['admin_list_count'] = Model\Admins::mGetPageCount($where);

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['admin_name'] = ['type' => 'input', 'name' => trans('common.admin') . trans('common.name')];
        $whereInfo['group_id']   = ['type' => 'input', 'name' => trans('common.group') . trans('common.name')];
        $whereInfo['last_time']  = ['type' => 'time', 'name' => trans('common.login') . trans('common.time')];
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

        $assign['title'] = trans('common.admin') . trans('common.management');
        return view('admin.', $assign);
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $data      = $this->makeData();
            $resultAdd = Model\Admins::mAdd($data);
            if ($resultAdd) {
                $this->success(trans('common.admin') . trans('common.add') . trans('common.success'), route('index'));

                return;
            } else {
                $this->error(trans('common.admin') . trans('common.add') . trans('common.error'), route('add'));
            }
        }
        $this->addEditCommon();
        $assign['title'] = trans('common.admin') . trans('common.add');
        return view('admin.addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = Model\Admins::mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('common.admin') . trans('common.edit') . trans('common.success'), route('index'));

                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('common.admin') . trans('common.edit') . trans('common.error'), $errorGoLink);
            }
        }

        $editInfo = Model\Admins::mFind($id);
        foreach ($editInfo['group_id'] as &$groupId) {
            $adminGroupName = Model\AdminGroup::mFindColumn($groupId, 'name');
            $groupId        = ['value' => $groupId, 'html' => $adminGroupName];
        }
        $editInfo['group_id'] = json_encode($editInfo['group_id']);
        $assign['edit_info']  = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.admin') . trans('common.edit');
        return view('admin.addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\Admins::mDel($id);
        if ($resultDel) {
            $this->success(trans('common.admin') . trans('common.del') . trans('common.success'), route('index'));

            return;
        } else {
            $this->error(trans('common.admin') . trans('common.del') . trans('common.error'), route('index'));
        }
    }

    //配置
    public function setting()
    {
        if (IS_POST) {
            //表单提交的名称
            $col = [
                'SYS_ADMIN_AUTO_LOG',
                'SYS_BACKEND_VERIFY',
                'SYS_BACKEND_TIMEOUT',
                'SYS_BACKEND_LOGIN_NUM',
                'SYS_BACKEND_LOCK_TIME',
            ];
            $this->_put_config($col, 'system');

            return;
        }

        $assign['title'] = trans('common.admin') . trans('common.config');
        return view('admin.', $assign);
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'admin_name':
                //不能为空
                if ('' == $data['admin_name']) {
                    $result['info'] = trans('common.admin') . trans('common.name') . trans('common.not') . trans('common.empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['admin_name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['admin_name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['admin_name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('common.name_format_error', ['string' => $matches[1]]);
                    break;
                }
                //检查用户名是否存在
                $adminInfo = Model\Admins::mSelect(['admin_name' => $data['admin_name'], 'id' => ['neq', $data['id']]]);
                if (0 < count($adminInfo)) {
                    $result['info'] = trans('admin') . trans('common.name') . trans('common.exists');
                    break;
                }
                break;
            case 'password':
                if ($data['is_pwd'] || '' != $data['password']) {
                    //不能为空
                    if ('' == $data['password']) {
                        $result['info'] = trans('common.pass') . trans('common.not') . trans('common.empty');
                        break;
                    }
                    //密码长度不能小于6
                    if (6 > strlen($data['password'])) {
                        $result['info'] = trans('common.pass_len_error');
                        break;
                    }
                }
                break;
            case 'password_again':
                if ($data['is_pwd'] || '' != $data['password'] || '' != $data['password_again']) {
                    //检测再一次输入的密码是否一致
                    if ($data['password'] != $data['password_again']) {
                        $result['info'] = trans('common.password_again_error');
                        break;
                    }
                    //不能为空
                    if ('' == $data['password_again']) {
                        $result['info'] = trans('common.pass') . trans('common.not') . trans('common.empty');
                        break;
                    }
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
                        $result['info'] = trans('common.privilege') . trans('common.submit') . trans('common.error');
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
            case 'group_id':
                if (1 != session('backend_info.id')) {
                    $where['manage_id'] = session('backend_info.id');
                }
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $adminGroupList = Model\AdminGroup::mSelect($where);
                foreach ($adminGroupList as $adminGroup) {
                    $result['info'][] = ['value' => $adminGroup['id'], 'html' => $adminGroup['name']];
                }
                break;
        }

        return $result;
    }

    //构造数据
    //$isPwd 是否检测密码规则
    private function makeData()
    {
        //初始化参数
        $id            = request('id');
        $adminName     = request('admin_name');
        $password      = request('password');
        $passwordAgain = request('password_again');
        $groupId       = request('group_id');
        $privilege     = request('privilege');
        $isEnable      = request('is_enable');

        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', ['id' => $id]);
        //检测初始化参数是否合法
        if ('add' == ACTION_NAME || null !== $adminName) {
            $result = $this->doValidateForm('admin_name', ['id' => $id, 'admin_name' => $adminName]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $isPwd  = ('add' == ACTION_NAME) ? true : false;
            $result = $this->doValidateForm('password', ['password' => $password, 'is_pwd' => $isPwd]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }
        if ('add' == ACTION_NAME || null !== $password) {
            $result = $this->doValidateForm('password_again', [
                'password'       => $password,
                'password_again' => $passwordAgain,
            ]);
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

        //最高级管理不检查该项 管理员可否被当前管理员添加编辑
        if (1 != session('backend_info.id')) {
            $mFindAllow = Model\AdminGroup::mFind_allow();
            if (!mInArray($groupId, $mFindAllow)) {
                $this->error(trans('common.you') . trans('common.none') . trans('common.privilege'));
            }

        }

        $data = [];
        ('add' == ACTION_NAME || null !== $adminName) && $data['admin_name'] = $adminName;
        ('add' == ACTION_NAME || null !== $password) && $data['admin_pwd'] = $password;
        ('add' == ACTION_NAME || null !== $groupId) && $data['group_id'] = $groupId;
        ('add' == ACTION_NAME || null !== $privilege) && $data['privilege'] = $privilege;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;

        return $data;
    }

    //构造管理员assign公共数据
    private function addEditCommon()
    {
        $assign['privilege'] = $this->getPrivilege('Admin', session('backend_info.privilege'));
    }
}