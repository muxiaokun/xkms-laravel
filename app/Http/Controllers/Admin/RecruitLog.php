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
        $whereValue                       = '';
        $whereValue                       = I('r_id');
        $whereValue && $where['r_id']     = $whereValue;
        $whereValue                       = I('name');
        $whereValue && $where['name']     = array('like', '%' . $whereValue . '%');
        $whereValue                       = mMktimeRange('birthday');
        $whereValue && $where['birthday'] = $whereValue;
        //初始化翻页 和 列表数据
        $recruitLogList         = $RecruitLogModel->mSelect($where, true);
        $recruitSexData         = L('recruit_sex_data');
        $recruitCertificateData = L('recruit_certificate_data');
        foreach ($recruitLogList as &$recruitLog) {
            $recruitLog['recruit_title'] = $RecruitModel->mFindColumn($recruitLog['r_id'], 'title');
            $recruitLog['sex']           = $recruitSexData[$recruitLog['sex']];
            $recruitLog['certificate']   = $recruitCertificateData[$recruitLog['certificate']];
        }
        $this->assign('recruit_log_list', $recruitLogList);
        $this->assign('recruit_log_list_count', $RecruitLogModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo             = array();
        $whereInfo['name']     = array('type' => 'input', 'name' => L('recruit_name'));
        $whereInfo['birthday'] = array('type' => 'time', 'name' => L('recruit_birthday'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle        = array();
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

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
        $resultDel      = $RecruitLogModel->mDel($id);
        if ($resultDel) {
            $this->success(L('recruit_log') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('recruit_log') . L('del') . L('error'), U('index'));
        }
    }

}
