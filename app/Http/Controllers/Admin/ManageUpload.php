<?php
// 后台 管理上传

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class ManageUpload extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $whereValue                        = '';
        $whereValue                        = request('suffix');
        $whereValue && $where['suffix']    = $whereValue;
        $whereValue                        = request('bind_info');
        $whereValue && $where['bind_info'] = array('like', '%|' . $whereValue . ':%');
        $whereValue                        = mMktimeRange('add_time');
        $whereValue && $where['add_time']  = $whereValue;

        //初始化翻页 和 列表数据
        $AdminModel         = D('Admin');
        $MemberModel        = D('Member');
        $ManageUploadModel  = D('ManageUpload');
        $manageUploadList = $ManageUploadModel->mSelect($where, true);
        foreach ($manageUploadList as &$manageUpload) {
            switch ($manageUpload['user_type']) {
                case 1:
                    $manageUpload['user_name'] = $AdminModel->mFindColumn($manageUpload['user_id'], 'admin_name');
                    break;
                case 2:
                    $manageUpload['user_name'] = $MemberModel->mFindColumn($manageUpload['user_id'], 'member_name');
                    break;
            }
            $bindInfo     = array(trans('controller') => trans('relevance') . trans('id'));
            $bindInfoArr = explode('|', $manageUpload['bind_info']);
            foreach ($bindInfoArr as $info) {
                if ($info) {
                    $bindInfoTmp                = explode(':', $info);
                    $bindInfo[$bindInfoTmp[0]] = $bindInfoTmp[1];
                }
            }
            $manageUpload['bind_info'] = json_encode($bindInfo);
        }
        $this->assign('manage_upload_list', $manageUploadList);
        $this->assign('manage_upload_list_count', $ManageUploadModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo              = array();
        $whereInfo['add_time']  = array('type' => 'time', 'name' => trans('add') . trans('time'));
        $whereInfo['suffix']    = array('type' => 'input', 'name' => trans('suffix'));
        $whereInfo['bind_info'] = array('type' => 'input', 'name' => trans('bind') . trans('controller'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('file') . trans('management'));
        $this->display();
    }

    //删除图片
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ManageUploadModel = D('ManageUpload');
        $resultDel        = $ManageUploadModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('file') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('file') . trans('del') . trans('error'), route('index'));
        }
    }

    //清除未用
    public function edit()
    {
        $lang = trans('yes') . trans('no') . trans('confirm') . trans('clear');
        if (!$this->showConfirm($lang)) {
            return;
        }

        $ManageUploadModel  = D('ManageUpload');
        $where['_string']   = '(bind_info is NULL OR bind_info = "")';
        $manageUploadList = $ManageUploadModel->mSelect($where, $ManageUploadModel->where($where)->count());
        foreach ($manageUploadList as $manageUpload) {
            $resultDel = $ManageUploadModel->mDel($manageUpload['id']);
            if (!$resultDel) {
                $this->error(trans('clear') . trans('file') . $manageUpload['path'] . trans('error'), route('index'));
            }
        }
        $this->success(trans('clear') . trans('file') . trans('success'), route('index'));
    }

    //上传接口实现
    public function UploadFile()
    {
        $CommonManageUploadController = new \Common\Controller\CommonManageUploadController();
        return $CommonManageUploadController->UploadFile();
    }

    //文件管理接口实现
    public function ManageFile()
    {
        $CommonManageUploadController = new \Common\Controller\CommonManageUploadController();
        return $CommonManageUploadController->ManageFile();
    }
}
