<?php
// 前台 考核

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;
use App\Model;
use Carbon\Carbon;

class Assess extends FrontendMember
{
    //列表
    public function index()
    {
        //初始化翻页 和 列表数据
        $assessList = Model\Assess::where(function ($query) {
            $currentTime = Carbon::now();
            $query->where('is_enable', '=', 1);
            $query->where('start_time', '<', $currentTime);
            $query->where('end_time', '>', $currentTime);
            $query->whereIn('group_level', [session('frontend_info.group_id')]);

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
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
        $assign['assess_list'] = $assessList;

        $this->commonAssgin();
        $assign['title'] = trans('assess.assess');
        return view('home.Assess_index', $assign);
    }

    //添加
    public function add()
    {
        //初始化和权限检测
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Home::Assess::index'));
        }

        $assessInfo = Model\Assess::colWhere($id)->first()->toArray();
        $currentTime = Carbon::now();
        if (
            1 != $assessInfo['is_enable'] ||
            !in_array($assessInfo['group_level'], session('frontend_info.group_id')) ||
            $currentTime < $assessInfo['start_time'] &&
            $currentTime > $assessInfo['end_time']
        ) {
            return $this->error(trans('common.you') . trans('common.none') . trans('common.privilege') . trans('assess.assess'),
                route('Home::Assess::index'));
        }

        if (request()->isMethod('POST')) {
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

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

        $assign['assess_info']  = $assessInfo;

        $this->commonAssgin();
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
                    $result['info'] = trans('assess.quest_error1');
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
        $result = ['status' => true, 'info' => []];
        switch ($field) {
            case 'member':
                Model\Member::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('member_name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['member_name']];
                });
                break;
            case 'member_group':
                Model\MemberGroup::where(function ($query) use ($data) {
                    if (isset($data['inserted'])) {
                        $query->whereNotIn('id', $data['inserted']);
                    }

                    if (isset($data['keyword'])) {
                        $query->where('name', 'like', '%' . $data['keyword'] . '%');
                    }

                })->get()->each(function ($item, $key) use (&$result) {
                    $result['info'][] = ['value' => $item['id'], 'html' => $item['name']];
                });
                break;
        }
        return $result;
    }

    //建立数据
    private function makeData($type)
    {
        $id        = request('id');
        $gradeId   = session('frontend_info.id');
        $reGradeId = request('re_grade_id');
        $score     = request('score');

        //检测初始化参数是否合法
        if ($id) {
            if (is_array($id)) {
                $errorGoLink = route('Admin::MemberGroup::index');
            } else {
                $errorGoLink = route('Admin::MemberGroup::edit', ['id' => $id]);
            }
        } else {
            $errorGoLink = route('Admin::MemberGroup::add');
        }

        $data = [];
        if ('add' == $type || null !== $id) {
            $data['assess_id'] = $id;
        }
        if ('add' == $type || null !== $gradeId) {
            $data['grade_id'] = $gradeId;
        }
        if ('add' == $type || null !== $reGradeId) {
            $result = $this->doValidateForm('re_grade_id', ['re_grade_id' => $reGradeId]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['re_grade_id'] = $reGradeId;
        }
        if ('add' == $type || null !== $score) {
            $data['score'] = $score;
        }
        return $data;
    }
}
