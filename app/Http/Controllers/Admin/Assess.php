<?php
// 后台 考核

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Assess extends Backend
{
    //列表
    public function index()
    {
        $AssessModel      = D('Assess');
        $MemberGroupModel = D('MemberGroup');
        $where            = [];
        //建立where
        $whereValue = '';
        $whereValue = request('title');
        $whereValue && $where['title'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('group_level');
        $whereValue && $where['group_level'] = $MemberGroupModel->where(['name' => ['like',
            '%' . $whereValue . '%']])->mColumn2Array('id');
        $whereValue = mMktimeRange('start_time');
        $whereValue && $where['start_time'] = $whereValue;
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $assessList = $AssessModel->mSelect($where, true);
        foreach ($assessList as &$assess) {
            $assess['group_name'] = ($assess['group_level']) ? $MemberGroupModel->mFindColumn($assess['group_level'], 'name') : trans('empty');
        }
        $this->assign('assess_list', $assessList);
        $this->assign('assess_list_count', $AssessModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo                = [];
        $whereInfo['title']       = ['type' => 'input', 'name' => trans('title')];
        $whereInfo['group_level'] = ['type' => 'input', 'name' => trans('assess') . trans('group')];
        $whereInfo['start_time']  = ['type' => 'time', 'name' => trans('add') . trans('time')];
        $whereInfo['is_enable']   = ['type'  => 'select',
                                     'name'  => trans('yes') . trans('no') . trans('enable'),
                                     'value' => [1 => trans('enable'), 2 => trans('disable')]];
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle             = [];
        $batchHandle['add']      = $this->_check_privilege('add');
        $batchHandle['edit']     = $this->_check_privilege('edit');
        $batchHandle['log_edit'] = $this->_check_privilege('edit', 'AssessLog');
        $batchHandle['del']      = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('assess') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $AssessModel = D('Assess');
            $data        = $this->makeData();
            $resultAdd   = $AssessModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('assess') . trans('add') . trans('success'), route('index'));

                return;
            } else {
                $this->error(trans('assess') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->assign('title', trans('assess') . trans('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $AssessModel = D('Assess');
        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = $AssessModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('assess') . trans('edit') . trans('success'), route('index'));

                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('assess') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo               = $AssessModel->mFind($id);
        $MemberGroupModel       = D('MemberGroup');
        $editInfo['group_name'] = $MemberGroupModel->mFindColumn($editInfo['group_level'], 'name');
        $this->assign('edit_info', $editInfo);

        $this->assign('title', trans('assess') . trans('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $AssessModel = D('Assess');
        $resultDel   = $AssessModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('assess') . trans('del') . trans('success'), route('index'));

            return;
        } else {
            $this->error(trans('assess') . trans('del') . trans('error'), route('index'));
        }
    }

    //异步数据获取
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'group_level':
                isset($data['inserted']) && $where['id'] = ['not in', $data['inserted']];
                isset($data['keyword']) && $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $MemberGroupModel  = D('MemberGroup');
                $memberGroupList = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
        }

        return $result;
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $id            = request('get.id');
        $title         = request('title');
        $explains      = request('explains');
        $groupLevel    = request('group_level');
        $startTime     = request('start_time');
        $endTime       = request('end_time');
        $startTime     = mMktime($startTime, true);
        $endTime       = mMktime($endTime, true);
        $isEnable      = request('is_enable');
        $target        = request('target');
        $extInfo       = [];
        $gradeProject = [];
        foreach (request('ext_info') as $value) {
            $gradeProject[] = json_decode(str_replace('&quot;', '"', $value), true);
        }
        $extInfo = json_encode($gradeProject);

        $data = [];
        ('add' == ACTION_NAME || null !== $title) && $data['title'] = $title;
        ('add' == ACTION_NAME || null !== $explains) && $data['explains'] = $explains;
        ('add' == ACTION_NAME || null !== $groupLevel) && $data['group_level'] = $groupLevel;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time'] = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time'] = $endTime;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;
        ('add' == ACTION_NAME || null !== $target) && $data['target'] = $target;
        ('add' == ACTION_NAME || null !== $extInfo) && $data['ext_info'] = $extInfo;

        return $data;
    }
}
