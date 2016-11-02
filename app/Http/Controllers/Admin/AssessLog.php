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
        $id = request('get.id');
        if (!$id) {
            return $this->error(trans('common.assess') . trans('common.id') . trans('common.error'),
                route('Assess/index'));
        }

        $assessInfo                  = Model\Assess::mFind($id);
        $assessInfo['all_grade']     = 0;
        $assessInfo['re_grade_name'] = '';
        $reGradeId                   = request('re_grade_id');

        switch ($assessInfo['target']) {
            case 'member':
                $reGradeName = Model\Member::mFindColumn($reGradeId, 'member_name');
                break;
            case 'member_group':
                $reGradeName = Model\MemberGroup::mFindColumn($reGradeId, 'name');
                break;
        }
        if ($reGradeName) {
            $where                = ['assess_id' => $id];
            $where['re_grade_id'] = $reGradeId;
            $countRow             = Model\AssessLog::mGetPageCount($where);
            $assessLogInfos       = Model\AssessLog::limit($countRow)->mList($where);
            //算出各项评分
            $resultInfo             = [];
            $assessInfo['ext_info'] = json_decode($assessInfo['ext_info'], true);
            foreach ($assessInfo['ext_info'] as $key => $value) {
                $resultInfo[$key]['p'] = $value['p'];
                $resultInfo[$key]['f'] = $value['f'];
                //处理合计分数
                foreach ($assessLogInfos as $assessLogInfo) {
                    $resultInfo[$key]['g'] += $assessLogInfo['score'][$key];
                }
                //平均分
                $resultInfo[$key]['g'] = round($resultInfo[$key]['g'] / $countRow);
                //总分
                $assessInfo['all_grade'] += $resultInfo[$key]['g'];
            }
            $assessInfo['re_grade_name'] = $memberInfo['member_name'];
            $assessInfo['result_info']   = $resultInfo;
        }
        $assign['assess_info'] = $assessInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.assess') . trans('common.statistics');
        return view('admin.', $assign);
    }

    //删除
    public function del()
    {
        $id = request('get.id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('edit', ['id' => $id]));
        }

        $resultDel = Model\AssessLog::mDel($id);
        if ($resultDel) {
            return $this->success(trans('common.assess') . trans('common.del') . trans('common.success'),
                route('Assess/index'));
            return;
        } else {
            return $this->error(trans('common.assess') . trans('common.del') . trans('common.error'),
                route('edit', ['id' => $id]));
        }
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
                $memberUserList = Model\Member::mList($where);
                //取出已经评价的
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = ['value' => $memberUser['id'], 'html' => $memberUser['member_name']];
                }
                break;
            case 'member_group':
                isset($data['keyword']) && $data['keyword'] = $where['name'] = ['like', '%' . $data['keyword'] . '%'];
                $memberGroupList = Model\MemberGroup::mList($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = ['value' => $memberGroup['id'], 'html' => $memberGroup['name']];
                }
                break;
        }
        return $result;
    }
}
