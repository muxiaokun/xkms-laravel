<?php
// 后台 留言板

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class MessageBoard extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];

        $messageBoardList = Model\MessageBoard::mList($where, true);
        foreach ($messageBoardList as &$messageBoard) {
            $option = [];
            foreach ($messageBoard['config'] as $name => $value) {
                $option[] = $name;
            }
            $messageBoard['option'] = mSubstr(implode(',', $option), 40);
        }
        $assign['message_board_list']       = $messageBoardList;

        //初始化batch_handle
        $batchHandle              = [];
        $batchHandle['log_index'] = $this->_check_privilege('index', 'MessageBoardLog');
        $batchHandle['add']       = $this->_check_privilege('add');
        $batchHandle['edit']      = $this->_check_privilege('edit');
        $batchHandle['del']       = $this->_check_privilege('del');
        $assign['batch_handle']   = $batchHandle;

        $assign['title'] = trans('common.messageboard') . trans('common.management');
        return view('admin.MessageBoard_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData();
            $resultAdd = Model\MessageBoard::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.messageboard') . trans('common.add') . trans('common.success'),
                    $rebackLink);
            } else {
                return $this->error(trans('common.messageboard') . trans('common.add') . trans('common.error'),
                    route('Admin::MessageBoard::add', ['cate_id' => request('get.cate_id')]));
            }
        }

        $assign['template_list'] = mScanTemplate('index', 'MessageBoard');
        $assign['title']         = trans('common.messageboard') . trans('common.add');
        return view('admin.MessageBoard_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::MessageBoard::index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData();
            $resultEdit = Model\MessageBoard::idWhere($id)->update($data);
            if ($resultEdit) {
                return $this->success(trans('common.messageboard') . trans('common.edit') . trans('common.success'),
                    route('Admin::MessageBoard::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::MessageBoard::index') : route('Admin::MessageBoard::edit',
                    ['id' => $id]);
                return $this->error(trans('common.messageboard') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo            = Model\MessageBoard::mFind($id);
        $editInfo['config']  = json_encode($editInfo['config']);
        $assign['edit_info'] = $editInfo;

        $assign['template_list'] = mScanTemplate('index', 'MessageBoard');
        $assign['title']         = trans('common.messageboard') . trans('common.edit');
        return view('admin.MessageBoard_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::MessageBoard::index'));
        }

        $resultDel = false;
        if (1 != $id || (is_array($id) && !in_array(1, $id))) {
            $resultDel = Model\MessageBoard::destroy($id);
        }
        if ($resultDel) {
            return $this->success(trans('common.messageboard') . trans('common.del') . trans('common.success'),
                route('Admin::MessageBoard::index'));
        } else {
            return $this->error(trans('common.messageboard') . trans('common.del') . trans('common.error'),
                route('Admin::MessageBoard::index'));
        }
    }

    //构造数据
    private function makeData()
    {
        $name     = request('name');
        $template = request('template');
        $config   = json_decode(htmlspecialchars_decode(request('config')), true);

        $data = [];
        ('add' == ACTION_NAME || null !== $name) && $data['name'] = $name;
        ('add' == ACTION_NAME || null !== $template) && $data['template'] = $template;
        ('add' == ACTION_NAME || null !== $config) && $data['config'] = $config;

        return $data;
    }
}
