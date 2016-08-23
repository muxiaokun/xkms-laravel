<?php
// 后台 留言板

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class MessageBoard extends Backend
{
    //列表
    public function index()
    {
        $MessageBoardModel = D('MessageBoard');
        $where             = array();

        //建立where
        $whereValue                   = '';
        $whereValue                   = I('name');
        $whereValue && $where['name'] = array('like', '%' . $whereValue . '%');

        $messageBoardList = $MessageBoardModel->mSelect($where, true);
        foreach ($messageBoardList as &$messageBoard) {
            $option = array();
            foreach ($messageBoard['config'] as $name => $value) {
                $option[] = $name;
            }
            $messageBoard['option'] = mSubstr(implode(',', $option), 40);
        }
        $this->assign('message_board_list', $messageBoardList);
        $this->assign('message_board_list_count', $MessageBoardModel->mGetPageCount($where));

        //初始化batch_handle
        $batchHandle              = array();
        $batchHandle['log_index'] = $this->_check_privilege('index', 'MessageBoardLog');
        $batchHandle['add']       = $this->_check_privilege('add');
        $batchHandle['edit']      = $this->_check_privilege('edit');
        $batchHandle['del']       = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('messageboard') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $data              = $this->makeData();
            $MessageBoardModel = D('MessageBoard');
            $resultAdd        = $MessageBoardModel->mAdd($data);
            if ($resultAdd) {
                $this->success(trans('messageboard') . L('add') . L('success'), $rebackLink);
                return;
            } else {
                $this->error(trans('messageboard') . L('add') . L('error'), route('add', array('cate_id' => I('get.cate_id'))));
            }
        }

        $this->assign('template_list', mScanTemplate('index', config('DEFAULT_MODULE'), 'MessageBoard'));
        $this->assign('title', trans('messageboard') . L('add'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(trans('id') . L('error'), route('index'));
        }

        $MessageBoardModel = D('MessageBoard');
        if (IS_POST) {
            $data        = $this->makeData();
            $resultEdit = $MessageBoardModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('messageboard') . L('edit') . L('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', array('id' => $id));
                $this->error(trans('messageboard') . L('edit') . L('error'), $errorGoLink);
            }
        }

        $editInfo           = $MessageBoardModel->mFind($id);
        $editInfo['config'] = json_encode($editInfo['config']);
        $this->assign('edit_info', $editInfo);

        $this->assign('template_list', mScanTemplate('index', config('DEFAULT_MODULE'), 'MessageBoard'));
        $this->assign('title', trans('messageboard') . L('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(trans('id') . L('error'), route('index'));
        }

        $MessageBoardModel = D('MessageBoard');
        $resultDel        = $MessageBoardModel->mDel($id);
        if ($resultDel) {
            $this->success(trans('messageboard') . L('del') . L('success'), route('index'));
            return;
        } else {
            $this->error(trans('messageboard') . L('del') . L('error'), route('index'));
        }
    }

    //构造数据
    private function makeData()
    {
        $name     = I('name');
        $template = I('template');
        $config   = json_decode(htmlspecialchars_decode(I('config')), true);

        $data                                                             = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']         = $name;
        ('add' == ACTION_NAME || null !== $template) && $data['template'] = $template;
        ('add' == ACTION_NAME || null !== $config) && $data['config']     = $config;

        return $data;
    }
}
