<?php
//后台 招聘记录

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class RecruitLog extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('r_id');
        $whereValue && $where[] = ['r_id', $whereValue];
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = mMktimeRange('birthday');
        $whereValue && $where[] = ['birthday', $whereValue];
        //初始化翻页 和 列表数据
        $recruitLogList         = Model\RecruitLog::where($where)->paginate(config('system.sys_max_row'));
        $recruitSexData         = trans('common.recruit_sex_data');
        $recruitCertificateData = trans('common.recruit_certificate_data');
        foreach ($recruitLogList as &$recruitLog) {
            $recruitLog['recruit_title'] = Model\Recruit::colWhere($recruitLog['r_id'])->first()['title'];
            $recruitLog['sex']           = $recruitSexData[$recruitLog['sex']];
            $recruitLog['certificate']   = $recruitCertificateData[$recruitLog['certificate']];
        }
        $assign['recruit_log_list']       = $recruitLogList;

        //初始化where_info
        $whereInfo             = [];
        $whereInfo['name'] = ['type' => 'input', 'name' => trans('recruit.recruit_name')];
        $whereInfo['birthday'] = ['type' => 'time', 'name' => trans('recruit.recruit_birthday')];
        $assign['where_info']  = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title']        = trans('recruit.recruit_log') . trans('common.management');
        return view('admin.RecruitLog_index', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::RecruitLog::index'));
        }

        $resultDel = Model\RecruitLog::destroy($id);
        if ($resultDel) {
            return $this->success(trans('common.recruit_log') . trans('common.del') . trans('common.success'),
                route('Admin::RecruitLog::index'));
        } else {
            return $this->error(trans('common.recruit_log') . trans('common.del') . trans('common.error'),
                route('Admin::RecruitLog::index'));
        }
    }

}
