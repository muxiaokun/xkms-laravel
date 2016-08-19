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
// 前台 考核

namespace App\Http\Controllers\Home;

use App\Http\Controllers\FrontendMember;

class Assess extends FrontendMember
{
    //列表
    public function index()
    {
        $AssessModel = D('Assess');

        $current_time = time();
        $where        = array(
            'group_level' => array('in', session('frontend_info.group_id')),
            'is_enable'   => 1,
            'start_time'  => array('lt', $current_time),
            'end_time'    => array('gt', $current_time),
        );

        //初始化翻页 和 列表数据
        $assess_list = $AssessModel->m_select($where, true);
        foreach ($assess_list as &$assess) {
            switch ($assess['target']) {
                case 'member':
                    $assess['target_name'] = L('member');
                    break;
                case 'member_group':
                    $assess['target_name'] = L('member') . L('group');
                    break;
            }
        }
        $this->assign('assess_list', $assess_list);
        $this->assign('assess_list_conut', $AssessModel->get_page_count($where));

        $this->assign('title', L('assess'));
        $this->display();
    }

    //添加
    public function add()
    {
        //初始化和权限检测
        $id = I('get.id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $AssessModel  = D('Assess');
        $assess_info  = $AssessModel->m_find($id);
        $current_time = time();
        if (
            1 != $assess_info['is_enable'] ||
            !in_array($assess_info['group_level'], session('frontend_info.group_id')) ||
            $current_time < $assess_info['start_time'] &&
            $current_time > $assess_info['end_time']
        ) {
            $this->error(L('you') . L('none') . L('privilege') . L('assess'), U('index'));
        }

        if (IS_POST) {
            $data = $this->_make_data();
            //提交时检测类型下可以被评分的组和组员
            $AssessLogMode = D('AssessLog');
            $result_add    = $AssessLogMode->m_add($data);
            if ($result_add) {
                $this->success(L('grade') . L('success'), U('index'));
                return;
            } else {
                $this->error(L('grade') . L('error'), U('add'));
            }
        }

        //初始化考核需要的数据
        switch ($assess_info['target']) {
            case 'member':
                $this->assign('member_list', true);
                break;
            case 'member_group':
                $this->assign('member_group_list', true);
                break;
        }

        $assess_info['ext_info'] = json_decode($assess_info['ext_info'], true);
        $this->assign('assess_info', $assess_info);
        $this->display();
    }

    //异步验证接口
    protected function _validform($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 're_grade_id':
                //不能为空
                if ('' == $data['re_grade_id']) {
                    $result['info'] = L('quest_error1');
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
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

    //建立数据
    private function _make_data()
    {
        $assess_id   = I('get.id');
        $grade_id    = session('frontend_info.id');
        $re_grade_id = I('re_grade_id');
        $score       = I('score');

        //检测初始化参数是否合法
        $error_go_link = (!$id) ? U('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $re_grade_id) {
            $result = $this->_validform('re_grade_id', array('re_grade_id' => $re_grade_id));
            if (!$result['status']) {
                $this->error($result['info'], $error_go_link);
            }

        }

        $data                                                                   = array();
        ('add' == ACTION_NAME || null !== $assess_id) && $data['assess_id']     = $assess_id;
        ('add' == ACTION_NAME || null !== $grade_id) && $data['grade_id']       = $grade_id;
        ('add' == ACTION_NAME || null !== $re_grade_id) && $data['re_grade_id'] = $re_grade_id;
        ('add' == ACTION_NAME || null !== $score) && $data['score']             = $score;
        return $data;
    }
}
