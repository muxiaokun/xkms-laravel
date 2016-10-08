<?php
// 后台 考核记录

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class AssessLog extends Backend
{
    //统计考核结果
    public function edit()
    {
        $id = request('get.id');
        if (!$id) {
            $this->error(trans('assess') . trans('id') . trans('error'), route('Assess/index'));
        }

        $AssessModel                  = D('Assess');
        $assessInfo                  = $AssessModel->mFind($id);
        $assessInfo['all_grade']     = 0;
        $assessInfo['re_grade_name'] = '';
        $reGradeId                  = request('re_grade_id');

        switch ($assessInfo['target']) {
            case 'member':
                $MemberModel   = D('Member');
                $reGradeName = $MemberModel->mFindColumn($reGradeId, 'member_name');
                break;
            case 'member_group':
                $MemberGroupModel = D('MemberGroup');
                $reGradeName    = $MemberGroupModel->mFindColumn($reGradeId, 'name');
                break;
        }
        if ($reGradeName) {
            $AssessLogModel       = D('AssessLog');
            $where                = array('assess_id' => $id);
            $where['re_grade_id'] = $reGradeId;
            $countRow            = $AssessLogModel->mGetPageCount($where);
            $assessLogInfos     = $AssessLogModel->limit($countRow)->mSelect($where);
            //算出各项评分
            $resultInfo             = array();
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
        $this->assign('assess_info', $assessInfo);

        //初始化batch_handle
        $batchHandle        = array();
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('assess') . trans('statistics'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = request('get.id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('edit', array('id' => $id)));
        }

        $AssessLogModel = D('AssessLog');
        $resultDel     = $AssessLogModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('assess') . trans('del') . trans('success'), route('Assess/index'));
            return;
        } else {
            $this->error(trans('assess') . trans('del') . trans('error'), route('edit', array('id' => $id)));
        }
    }

    //异步获取数据接口
    protected function getData($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'member':
                $MemberModel                                = D('Member');
                isset($data['keyword']) && $data['keyword'] = $where['member_name'] = array('like', '%' . $data['keyword'] . '%');
                $memberUserList                           = $MemberModel->mSelect($where);
                //取出已经评价的
                $AssessLogMode = D('AssessLog');
                foreach ($memberUserList as $memberUser) {
                    $result['info'][] = array('value' => $memberUser['id'], 'html' => $memberUser['member_name']);
                }
                break;
            case 'member_group':
                $MemberGroupModel                           = D('MemberGroup');
                isset($data['keyword']) && $data['keyword'] = $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $memberGroupList                          = $MemberGroupModel->mSelect($where);
                foreach ($memberGroupList as $memberGroup) {
                    $result['info'][] = array('value' => $memberGroup['id'], 'html' => $memberGroup['name']);
                }
                break;
        }
        return $result;
    }
}
