<?php
// 后台 会员组

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class MemberGroup extends Backend
{
    //列表
    public function index()
    {
        $where            = [];

        //建立where
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $memberGroupList                   = Model\MemberGroup::mSelect($where, true);
        $assign['member_group_list']       = $memberGroupList;
        $assign['member_group_list_count'] = Model\MemberGroup::mGetPageCount($where);

        //初始化where_info
        $whereInfo              = [];
        $whereInfo['name']      = ['type' => 'input', 'name' => trans('common.group') . trans('common.name')];
        $whereInfo['is_enable'] = ['type'  => 'select',
                                   'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
                                   'value' => [1 => trans('common.enable'), 2 => trans('common.disable')],
        ];
        $assign['where_info']   = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.member') . trans('common.group') . trans('common.management');
        return view('admin.', $assign);
    }

    //新增
    public function add()
    {
        if ('POST' == request()->getMethod()) {
            $data      = $this->makeData();
            $resultAdd = Model\MemberGroup::mAdd($data);
            if ($resultAdd) {
                $this->success(trans('common.member') . trans('common.group') . trans('common.add') . trans('common.success'),
                    route('index'));
                return;
            } else {
                $this->error(trans('common.member') . trans('common.group') . trans('common.add') . trans('common.error'),
                    route('add'));
            }
        }

        $this->addEditCommon();
        $assign['title'] = trans('common.member') . trans('common.group') . trans('common.add');
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
            $resultEdit = Model\MemberGroup::mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('common.member') . trans('common.group') . trans('common.edit') . trans('common.success'),
                    route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('common.member') . trans('common.group') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }
        //获取分组默认信息
        $editInfo = Model\MemberGroup::mFind($id);
        foreach ($editInfo['manage_id'] as $manageKey => $manageId) {
            $memberName                        = Model\Member::mFindColumn($manageId, 'member_name');
            $editInfo['manage_id'][$manageKey] = ['value' => $manageId, 'html' => $memberName];
        }
        $editInfo['manage_id'] = json_encode($editInfo['manage_id']);
        $assign['edit_info']   = $editInfo;

        $this->addEditCommon();
        $assign['title'] = trans('common.member') . trans('common.group') . trans('common.edit');
        return view('admin.addedit', $assign);
    }

    //删除
    public function del()
    {

        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\MemberGroup::mDel($id);
        if ($resultDel) {
            //删除成功后 删除管理员与组的关系
            Model\Member::mClean($id, 'group_id');
            $this->success(trans('common.member') . trans('common.group') . trans('common.del') . trans('common.success'),
                route('index'));
            return;
        } else {
            $this->error(trans('common.member') . trans('common.group') . trans('common.del') . trans('common.error'),
                route('index'));
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
                    $result['info'] = trans('common.member') . trans('common.group') . trans('common.name') . trans('common.not') . trans('common.empty');
                    break;
                }
                //检查用户名规则
                if ('utf-8' != config('DEFAULT_CHARSET')) {
                    $data['name'] = iconv(config('DEFAULT_CHARSET'), 'utf-8', $data['name']);
                }

                preg_match('/([^\x80-\xffa-zA-Z0-9\s]*)/', $data['name'], $matches);
                if ('' != $matches[1]) {
                    $result['info'] = trans('common.name_format_error', ['string' => $matches[1]]);
                    break;
                }
                //检查管理组名是否存在
                $memberInfo = Model\MemberGroup::mSelect(['name' => $data['name'], 'id' => ['neq', $data['id']]]);
                if (0 < count($memberInfo)) {
                    $result['info'] = trans('member') . trans('common.group') . trans('common.name') . trans('common.exists');
                    break;
                }
                break;
            case 'privilege':
                //对比权限
                $privilege      = $this->getPrivilege('Home');
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
            case 'manage_id':
                isset($data['keyword']) && $where['member_name'] = ['like', '%' . $data['keyword'] . '%'];
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                $memberUserList = Model\Member::mSelect($where);
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
        }
        return $result;
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $id        = request('id');
        $manageId  = request('manage_id');
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

    //构造assign公共数据
    private function addEditCommon()
    {
        $assign['privilege'] = $this->getPrivilege('Home');
    }
}
