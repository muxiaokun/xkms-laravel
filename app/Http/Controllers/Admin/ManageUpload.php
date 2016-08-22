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
        $whereValue                        = I('suffix');
        $whereValue && $where['suffix']    = $whereValue;
        $whereValue                        = I('bind_info');
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
            $bindInfo     = array(L('controller') => L('relevance') . L('id'));
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
        $whereInfo['add_time']  = array('type' => 'time', 'name' => L('add') . L('time'));
        $whereInfo['suffix']    = array('type' => 'input', 'name' => L('suffix'));
        $whereInfo['bind_info'] = array('type' => 'input', 'name' => L('bind') . L('controller'));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', L('file') . L('management'));
        $this->display();
    }

    //删除图片
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $ManageUploadModel = D('ManageUpload');
        $resultDel        = $ManageUploadModel->mDel($id);
        if ($resultDel) {
            $this->success(L('file') . L('del') . L('success'), U('index'));
            return;
        } else {
            $this->error(L('file') . L('del') . L('error'), U('index'));
        }
    }

    //清除未用
    public function edit()
    {
        $lang = L('yes') . L('no') . L('confirm') . L('clear');
        if (!$this->showConfirm($lang)) {
            return;
        }

        $ManageUploadModel  = D('ManageUpload');
        $where['_string']   = '(bind_info is NULL OR bind_info = "")';
        $manageUploadList = $ManageUploadModel->mSelect($where, $ManageUploadModel->where($where)->count());
        foreach ($manageUploadList as $manageUpload) {
            $resultDel = $ManageUploadModel->mDel($manageUpload['id']);
            if (!$resultDel) {
                $this->error(L('clear') . L('file') . $manageUpload['path'] . L('error'), U('index'));
            }
        }
        $this->success(L('clear') . L('file') . L('success'), U('index'));
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
