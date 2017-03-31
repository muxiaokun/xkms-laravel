<?php
// 后台 考核记录

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class AssessLog extends Backend
{
    //统计考核结果
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('assess.assess') . trans('common.id') . trans('common.error'),
                route('Admin::AssessLog::index'));
        }

        $assessInfo = Model\Assess::colWhere($id)->first();
        $assessInfo['all_grade']     = 0;
        $assessInfo['re_grade_name'] = '';
        $reGradeId                   = request('re_grade_id');

        $where[]  = ['assess_id', '=', $id];
        $where[]  = ['re_grade_id', '=', $reGradeId];
        $countRow = Model\AssessLog::where($where)->count();
        if (0 < $countRow) {
            $assessLogInfos = Model\AssessLog::where($where)->get();
            //算出各项评分
            $resultInfo             = [];
            foreach ($assessInfo['ext_info'] as $key => $value) {
                $resultInfo[$key]['p'] = $value['p'];
                $resultInfo[$key]['f'] = $value['f'];
                //处理合计分数
                $resultInfo[$key]['g'] = 0;
                foreach ($assessLogInfos as $assessLogInfo) {
                    $resultInfo[$key]['g'] += $assessLogInfo['score'][$key];
                }
                //平均分
                $resultInfo[$key]['g'] = round($resultInfo[$key]['g'] / $countRow);
                //总分
                $assessInfo['all_grade'] += $resultInfo[$key]['g'];
            }
            $memberInfo = Model\Member::colWhere($assessInfo['grade_id'])->first();
            if (null === $memberInfo) {
                $assessInfo['re_grade_name'] = trans('common.member') . trans('common.not') . trans('common.exists');
            } else {
                $assessInfo['re_grade_name'] = $memberInfo['member_name'];
            }

            $assessInfo['result_info']   = $resultInfo;
        }
        $assign['assess_info'] = $assessInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['del']     = $this->_check_privilege('Admin::AssessLog::del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('assess.assess') . trans('common.statistics');
        return view('admin.AssessLog_edit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'),
                route('Admin::AssessLog::edit', ['id' => $id]));
        }

        $resultDel = Model\AssessLog::where('assess_id', '=', $id)->delete();
        if ($resultDel) {
            return $this->success(trans('assess.assess') . trans('common.record') . trans('common.del') . trans('common.success'),
                route('Admin::Assess::index'));
        } else {
            return $this->error(trans('assess.assess') . trans('common.record') . trans('common.del') . trans('common.error'),
                route('Admin::AssessLog::edit', ['id' => $id]));
        }
    }

    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = [];
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
}
