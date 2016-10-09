<?php
// 前台 考核

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Assess extends FrontendMember
{
    //列表
    public function index()
    {
        $AssessModel = D('Assess');

        $currentTime = time();
        $where       = [
            'group_level' => ['in', session('frontend_info.group_id')],
            'is_enable'   => 1,
            'start_time'  => ['lt', $currentTime],
            'end_time'    => ['gt', $currentTime],
        ];

        //初始化翻页 和 列表数据
        $assessList = $AssessModel->mSelect($where, true);
        foreach ($assessList as &$assess) {
            switch ($assess['target']) {
                case 'member':
                    $assess['target_name'] = trans('member');
                    break;
                case 'member_group':
                    $assess['target_name'] = trans('member') . trans('group');
                    break;
            }
        }
        $this->assign('assess_list', $assessList);
        $this->assign('assess_list_conut', $AssessModel->mGetPageCount($where));

        $this->assign('title', trans('assess'));
        $this->display();
    }

    //添加
    public function add()
    {
        //初始化和权限检测
        $id = request('get.id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $AssessModel = D('Assess');
        $assessInfo  = $AssessModel->mFind($id);
        $currentTime = time();
        if (
            1 != $assessInfo['is_enable'] ||
            !in_array($assessInfo['group_level'], session('frontend_info.group_id')) ||
            $currentTime < $assessInfo['start_time'] &&
            $currentTime > $assessInfo['end_time']
        ) {
            $this->error(trans('you') . trans('none') . trans('privilege') . trans('assess'), route('index'));
        }

        if (IS_POST) {
            $data = $this->makeData();
            //提交时检测类型下可以被评分的组和组员
            $AssessLogMode = D('AssessLog');
            $resultAdd     = $AssessLogMode->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('grade') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('grade') . trans('error'), route('add'));
            }
        }

        //初始化考核需要的数据
        switch ($assessInfo['target']) {
            case 'member':
                $this->assign('member_list', true);
                break;
            case 'member_group':
                $this->assign('member_group_list', true);
                break;
        }

        $assessInfo['ext_info'] = json_decode($assessInfo['ext_info'], true);
        $this->assign('assess_info', $assessInfo);
        $this->display();
    }

    //异步验证接口
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 're_grade_id':
                //不能为空
                if ('' == $data['re_grade_id']) {
                    $result['info'] = trans('quest_error1');
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = [];
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'member':
                $MemberModel = D('Member');
                isset($data['keyword']) && $data['keyword'] = $where['member_name'] = [
                    'like',
                    '%' . $data['keyword'] . '%',
                ];
                $memberUserList = $MemberModel->mSelect($where);
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
            case 'member_group':
                $MemberGroupModel = D('MemberGroup');
                isset($data['keyword']) && $data['keyword'] = $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
        }
        return $result;
    }

    //建立数据
    private function makeData()
    {
        $assessId  = request('get.id');
        $gradeId   = session('frontend_info.id');
        $reGradeId = request('re_grade_id');
        $score     = request('score');

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', ['id' => $id]);
        if ('add' == ACTION_NAME || null !== $reGradeId) {
            $result = $this->doValidateForm('re_grade_id', ['re_grade_id' => $reGradeId]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data = [];
        ('add' == ACTION_NAME || null !== $assessId) && $data['assess_id'] = $assessId;
        ('add' == ACTION_NAME || null !== $gradeId) && $data['grade_id'] = $gradeId;
        ('add' == ACTION_NAME || null !== $reGradeId) && $data['re_grade_id'] = $reGradeId;
        ('add' == ACTION_NAME || null !== $score) && $data['score'] = $score;
        return $data;
    }
}
