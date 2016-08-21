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
//后台 招聘记录

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class RecruitLog extends Backend
{
    //列表
    public function index()
    {
        $RecruitModel    = D('Recruit');
        $RecruitLogModel = D('RecruitLog');
        //建立where
        $v_value                       = '';
        $v_value                       = I('r_id');
        $v_value && $where['r_id']     = $v_value;
        $v_value                       = I('name');
        $v_value && $where['name']     = array('like', '%' . $v_value . '%');
        $v_value                       = M_mktime_range('birthday');
        $v_value && $where['birthday'] = $v_value;
        //初始化翻页 和 列表数据
        $recruit_log_list         = $RecruitLogModel->mSelect($where, true);
        $recruit_sex_data         = L('recruit_sex_data');
        $recruit_certificate_data = L('recruit_certificate_data');
        foreach ($recruit_log_list as &$recruit_log) {
            $recruit_log['recruit_title'] = $RecruitModel->mFindColumn($recruit_log['r_id'], 'title');
            $recruit_log['sex']           = $recruit_sex_data[$recruit_log['sex']];
            $recruit_log['certificate']   = $recruit_certificate_data[$recruit_log['certificate']];
        }
        $this->assign('recruit_log_list', $recruit_log_list);
        $this->assign('recruit_log_list_count', $RecruitLogModel->getPageCount($where));

        //初始化where_info
        $where_info             = array();
        $where_info['name']     = array('type' => 'input', 'name' => L('recruit_name'));
        $where_info['birthday'] = array('type' => 'time', 'name' => L('recruit_birthday'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle        = array();
        $batch_handle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('recruit_log') . L('management'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $RecruitLogModel = D('RecruitLog');
        $result_del      = $RecruitLogModel->mDel($id);
        if ($result_del) {
            $this->success(L('recruit_log') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('recruit_log') . L('del') . L('error'), U('index'));
        }
    }

}
