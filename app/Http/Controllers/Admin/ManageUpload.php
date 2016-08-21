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
// 后台 管理上传

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class ManageUpload extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $v_value                        = '';
        $v_value                        = I('suffix');
        $v_value && $where['suffix']    = $v_value;
        $v_value                        = I('bind_info');
        $v_value && $where['bind_info'] = array('like', '%|' . $v_value . ':%');
        $v_value                        = M_mktime_range('add_time');
        $v_value && $where['add_time']  = $v_value;

        //初始化翻页 和 列表数据
        $AdminModel         = D('Admin');
        $MemberModel        = D('Member');
        $ManageUploadModel  = D('ManageUpload');
        $manage_upload_list = $ManageUploadModel->mSelect($where, true);
        foreach ($manage_upload_list as &$manage_upload) {
            switch ($manage_upload['user_type']) {
                case 1:
                    $manage_upload['user_name'] = $AdminModel->mFindColumn($manage_upload['user_id'], 'admin_name');
                    break;
                case 2:
                    $manage_upload['user_name'] = $MemberModel->mFindColumn($manage_upload['user_id'], 'member_name');
                    break;
            }
            $bind_info     = array(L('controller') => L('relevance') . L('id'));
            $bind_info_arr = explode('|', $manage_upload['bind_info']);
            foreach ($bind_info_arr as $info) {
                if ($info) {
                    $bind_info_tmp                = explode(':', $info);
                    $bind_info[$bind_info_tmp[0]] = $bind_info_tmp[1];
                }
            }
            $manage_upload['bind_info'] = json_encode($bind_info);
        }
        $this->assign('manage_upload_list', $manage_upload_list);
        $this->assign('manage_upload_list_count', $ManageUploadModel->getPageCount($where));

        //初始化where_info
        $where_info              = array();
        $where_info['add_time']  = array('type' => 'time', 'name' => L('add') . L('time'));
        $where_info['suffix']    = array('type' => 'input', 'name' => L('suffix'));
        $where_info['bind_info'] = array('type' => 'input', 'name' => L('bind') . L('controller'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle         = array();
        $batch_handle['edit'] = $this->_check_privilege('edit');
        $batch_handle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

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
        $result_del        = $ManageUploadModel->mDel($id);
        if ($result_del) {
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
        if (!$this->show_confirm($lang)) {
            return;
        }

        $ManageUploadModel  = D('ManageUpload');
        $where['_string']   = '(bind_info is NULL OR bind_info = "")';
        $manage_upload_list = $ManageUploadModel->mSelect($where, $ManageUploadModel->where($where)->count());
        foreach ($manage_upload_list as $manage_upload) {
            $result_del = $ManageUploadModel->mDel($manage_upload['id']);
            if (!$result_del) {
                $this->error(L('clear') . L('file') . $manage_upload['path'] . L('error'), U('index'));
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
