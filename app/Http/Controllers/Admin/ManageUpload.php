<?php
// 后台 管理上传

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Http\Controllers\CommonManageUpload;
use App\Model;

class ManageUpload extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('suffix');
        $whereValue && $where[] = ['suffix', $whereValue];
        $whereValue = request('bind_info');
        $whereValue && $where['bind_info'] = ['like', '%|' . $whereValue . ':%'];
        $whereValue = mMktimeRange('add_time');
        $whereValue && $where[] = ['add_time', $whereValue];

        //初始化翻页 和 列表数据
        $manageUploadList = Model\ManageUpload::where($where)->paginate(config('system.sys_max_row'));
        foreach ($manageUploadList as &$manageUpload) {
            switch ($manageUpload['user_type']) {
                case 1:
                    $manageUpload['user_name'] = Model\Admins::colWhere($manageUpload['user_id'])->first()['admin_name'];
                    break;
                case 2:
                    $manageUpload['user_name'] = Model\Member::colWhere($manageUpload['user_id'])->first()['member_name'];
                    break;
            }
            $bindInfo    = [trans('common.controller') => trans('common.relevance') . trans('common.id')];
            $bindInfoArr = explode('|', $manageUpload['bind_info']);
            foreach ($bindInfoArr as $info) {
                if ($info) {
                    $bindInfoTmp               = explode(':', $info);
                    $bindInfo[$bindInfoTmp[0]] = $bindInfoTmp[1];
                }
            }
            $manageUpload['bind_info'] = json_encode($bindInfo);
        }
        $assign['manage_upload_list']       = $manageUploadList;

        //初始化where_info
        $whereInfo              = [];
        $whereInfo['add_time']  = ['type' => 'time', 'name' => trans('common.add') . trans('common.time')];
        $whereInfo['suffix']    = ['type' => 'input', 'name' => trans('common.suffix')];
        $whereInfo['bind_info'] = ['type' => 'input', 'name' => trans('common.bind') . trans('common.info')];
        $assign['where_info']   = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.file') . trans('common.management');
        return view('admin.ManageUpload_index', $assign);
    }

    //删除图片
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ManageUpload::index'));
        }

        $resultDel = Model\ManageUpload::deleteFile($id);
        if ($resultDel) {
            return $this->success(trans('common.file') . trans('common.del') . trans('common.success'),
                route('Admin::ManageUpload::index'));
        } else {
            return $this->error(trans('common.file') . trans('common.del') . trans('common.error'),
                route('Admin::ManageUpload::index'));
        }
    }

    //清除未用
    public function edit()
    {
        $lang = trans('common.yes') . trans('common.no') . trans('common.confirm') . trans('common.clear');
        if (!$this->showConfirm($lang)) {
            return;
        }

        $where['_string'] = '(bind_info is NULL OR bind_info = "")';
        $manageUploadList = Model\ManageUpload::where($where)->all();
        foreach ($manageUploadList as $manageUpload) {
            $resultDel = Model\ManageUpload::deleteFile($manageUpload['id']);
            if (!$resultDel) {
                return $this->error(trans('common.clear') . trans('common.file') . $manageUpload['path'] . trans('common.error'),
                    route('Admin::ManageUpload::index'));
            }
        }
        return $this->success(trans('common.clear') . trans('common.file') . trans('common.success'),
            route('Admin::ManageUpload::index'));
    }

    //上传接口实现
    public function UploadFile()
    {
        $CommonManageUpload = new CommonManageUpload();
        return $CommonManageUpload->UploadFile();
    }

    //文件管理接口实现
    public function ManageFile()
    {
        $CommonManageUpload = new CommonManageUpload();
        return $CommonManageUpload->ManageFile();
    }
}
