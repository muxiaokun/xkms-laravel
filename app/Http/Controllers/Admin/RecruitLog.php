<?php
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
        $whereValue                       = request('r_id');
        $whereValue && $where['r_id']     = $whereValue;
        $whereValue                       = request('name');
        $whereValue && $where['name']     = array('like', '%' . $whereValue . '%');
        $whereValue                       = mMktimeRange('birthday');
        $whereValue && $where['birthday'] = $whereValue;
        //初始化翻页 和 列表数据
        $recruitLogList         = $RecruitLogModel->mSelect($where, true);
        $recruitSexData         = trans('recruit_sex_data');
        $recruitCertificateData = trans('recruit_certificate_data');
        foreach ($recruitLogList as &$recruitLog) {
            $recruitLog['recruit_title'] = $RecruitModel->mFindColumn($recruitLog['r_id'], 'title');
            $recruitLog['sex']           = $recruitSexData[$recruitLog['sex']];
            $recruitLog['certificate']   = $recruitCertificateData[$recruitLog['certificate']];
        }
        $this->assign('recruit_log_list', $recruitLogList);
        $this->assign('recruit_log_list_count', $RecruitLogModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo             = array();
        $whereInfo['name']     = array('type' => 'input', 'name' => trans('recruit_name'));
        $whereInfo['birthday'] = array('type' => 'time', 'name' => trans('recruit_birthday'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle        = array();
        $batchHandle['del'] = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('recruit_log') . trans('management'));
        $this->display();
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $RecruitLogModel = D('RecruitLog');
        $resultDel      = $RecruitLogModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('recruit_log') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('recruit_log') . trans('del') . trans('error'), route('index'));
        }
    }

}
