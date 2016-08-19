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
// 后台 考核记录

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class AssessLog extends Backend
{
    //统计考核结果
    public function edit()
    {
        $id = I('get.id');
        if (!$id) {
            $this->error(L('assess') . L('id') . L('error'), U('Assess/index'));
        }

        $AssessModel                  = D('Assess');
        $assess_info                  = $AssessModel->m_find($id);
        $assess_info['all_grade']     = 0;
        $assess_info['re_grade_name'] = '';
        $re_grade_id                  = I('re_grade_id');

        switch ($assess_info['target']) {
            case 'member':
                $MemberModel   = D('Member');
                $re_grade_name = $MemberModel->m_find_column($re_grade_id, 'member_name');
                break;
            case 'member_group':
                $MemberGroupModel = D('MemberGroup');
                $re_grade_name    = $MemberGroupModel->m_find_column($re_grade_id, 'name');
                break;
        }
        if ($re_grade_name) {
            $AssessLogModel       = D('AssessLog');
            $where                = array('assess_id' => $id);
            $where['re_grade_id'] = $re_grade_id;
            $count_row            = $AssessLogModel->get_page_count($where);
            $assess_log_infos     = $AssessLogModel->limit($count_row)->m_select($where);
            //算出各项评分
            $result_info             = array();
            $assess_info['ext_info'] = json_decode($assess_info['ext_info'], true);
            foreach ($assess_info['ext_info'] as $key => $value) {
                $result_info[$key]['p'] = $value['p'];
                $result_info[$key]['f'] = $value['f'];
                //处理合计分数
                foreach ($assess_log_infos as $assess_log_info) {
                    $result_info[$key]['g'] += $assess_log_info['score'][$key];
                }
                //平均分
                $result_info[$key]['g'] = round($result_info[$key]['g'] / $count_row);
                //总分
                $assess_info['all_grade'] += $result_info[$key]['g'];
            }
            $assess_info['re_grade_name'] = $member_info['member_name'];
            $assess_info['result_info']   = $result_info;
        }
        $this->assign('assess_info', $assess_info);

        //初始化batch_handle
        $batch_handle        = array();
        $batch_handle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('assess') . L('statistics'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = I('get.id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('edit', array('id' => $id)));
        }

        $AssessLogModel = D('AssessLog');
        $result_del     = $AssessLogModel->m_del($id);
        if ($result_del) {
            $this->success(L('assess') . L('del') . L('success'), U('Assess/index'));
            return;
        } else {
            $this->error(L('assess') . L('del') . L('error'), U('edit', array('id' => $id)));
        }
    }

    //异步获取数据接口
    protected function _get_data($field, $data)
    {
        $where  = array();
        $result = array('status' => true, 'info' => array());
        switch ($field) {
            case 'member':
                $MemberModel                                = D('Member');
                isset($data['keyword']) && $data['keyword'] = $where['member_name'] = array('like', '%' . $data['keyword'] . '%');
                $member_user_list                           = $MemberModel->m_select($where);
                //取出已经评价的
                $AssessLogMode = D('AssessLog');
                foreach ($member_user_list as $member_user) {
                    $result['info'][] = array('value' => $member_user['id'], 'html' => $member_user['member_name']);
                }
                break;
            case 'member_group':
                $MemberGroupModel                           = D('MemberGroup');
                isset($data['keyword']) && $data['keyword'] = $where['name'] = array('like', '%' . $data['keyword'] . '%');
                $member_group_list                          = $MemberGroupModel->m_select($where);
                foreach ($member_group_list as $member_group) {
                    $result['info'][] = array('value' => $member_group['id'], 'html' => $member_group['name']);
                }
                break;
        }
        return $result;
    }
}
