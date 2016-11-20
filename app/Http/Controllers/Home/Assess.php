<?php
// 前台 考核

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;
use App\Model;

class Assess extends FrontendMember
{
    //列表
    public function index()
    {

        $currentTime = Carbon::now();
        $where       = [
            'group_level' => ['in', session('frontend_info.group_id')],
            'is_enable'   => 1,
            'start_time'  => ['lt', $currentTime],
            'end_time'    => ['gt', $currentTime],
        ];

        //初始化翻页 和 列表数据
        $assessList = Model\Assess::where($where)->ordered()->paginate(config('system.sys_max_row'));
        foreach ($assessList as &$assess) {
            switch ($assess['target']) {
                case 'member':
                    $assess['target_name'] = trans('common.member');
                    break;
                case 'member_group':
                    $assess['target_name'] = trans('common.member') . trans('common.group');
                    break;
            }
        }
        $assign['assess_list']       = $assessList;

        $assign['title'] = trans('common.assess');
        return view('home.Assess_index', $assign);
    }

    //添加
    public function add()
    {
        //初始化和权限检测
        $id = request('get.id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Home::Assess::index'));
        }

        $assessInfo  = Model\Assess::where('id', $id)->first();
        $currentTime = Carbon::now();
        if (
            1 != $assessInfo['is_enable'] ||
            !in_array($assessInfo['group_level'], session('frontend_info.group_id')) ||
            $currentTime < $assessInfo['start_time'] &&
            $currentTime > $assessInfo['end_time']
        ) {
            return $this->error(trans('common.you') . trans('common.none') . trans('common.privilege') . trans('common.assess'),
                route('Home::Assess::index'));
        }

        if (request()->isMethod('POST')) {
            $data = $this->makeData();
            //提交时检测类型下可以被评分的组和组员
            $where = [];
            if (isset($data['a_id']) && isset($data['grade_id']) && isset($data['re_grade_id'])) {
                $where = [
                    'a_id'        => $data['a_id'],
                    'grade_id'    => $data['grade_id'],
                    're_grade_id' => $data['re_grade_id'],
                ];
            }
            $resultAdd = Model\AssessLog::updateOrCreate($where, $data);
            if ($resultAdd) {
                return $this->success(trans('common.grade') . trans('common.success'), route('Home::Assess::index'));
            } else {
                return $this->error(trans('common.grade') . trans('common.error'), route('Home::Assess::add'));
            }
        }

        //初始化考核需要的数据
        switch ($assessInfo['target']) {
            case 'member':
                $assign['member_list'] = true;
                break;
            case 'member_group':
                $assign['member_group_list'] = true;
                break;
        }

        $assessInfo['ext_info'] = json_decode($assessInfo['ext_info'], true);
        $assign['assess_info']  = $assessInfo;
        return view('home.Assess_add', $assign);
    }

    //异步验证接口
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 're_grade_id':
                //不能为空
                if ('' == $data['re_grade_id']) {
                    $result['info'] = trans('common.quest_error1');
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
                isset($data['keyword']) && $data['keyword'] = $where['member_name'] = [
                    'like',
                    '%' . $data['keyword'] . '%',
                ];
                $memberUserList = Model\Member::where($where)->get();
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
            case 'member_group':
                isset($data['keyword']) && $data['keyword'] = $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = Model\MemberGroup::where($where)->get();
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
        $errorGoLink = (!$assessId) ? route('Home::Assess::add') : (is_array($id)) ? route('Home::Assess::index') : route('Home::Assess::edit',
            ['id' => $id]);
        if ('add' == ACTION_NAME || null !== $reGradeId) {
            $result = $this->doValidateForm('re_grade_id', ['re_grade_id' => $reGradeId]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
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
