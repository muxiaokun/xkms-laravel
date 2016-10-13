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
        $where             = [];

        //建立where
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];

        $messageBoardList = Model\MessageBoard::mSelect($where, true);
        foreach ($messageBoardList as &$messageBoard) {
            $option = [];
            foreach ($messageBoard['config'] as $name => $value) {
                $option[] = $name;
            }
            $messageBoard['option'] = mSubstr(implode(',', $option), 40);
        }
        $assign['message_board_list']       = $messageBoardList;
        $assign['message_board_list_count'] = Model\MessageBoard::mGetPageCount($where);

        //初始化batch_handle
        $batchHandle              = [];
        $batchHandle['log_index'] = $this->_check_privilege('index', 'MessageBoardLog');
        $batchHandle['add']       = $this->_check_privilege('add');
        $batchHandle['edit']      = $this->_check_privilege('edit');
        $batchHandle['del']       = $this->_check_privilege('del');
        $assign['batch_handle']   = $batchHandle;

        $assign['title'] = trans('common.messageboard') . trans('common.management');
        return view(');
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $data              = $this->makeData(', $assign);
        $resultAdd = Model\MessageBoard::mAdd($data);
            if ($resultAdd) {
                $this->success(trans('common.messageboard') . trans('common.add') . trans('common.success'),
                    $rebackLink);
                return;
            } else {
                $this->error(trans('common.messageboard') . trans('common.add') . trans('common.error'),
                    route('add', ['cate_id' => request('get.cate_id')]));
            }
        }

$assign['template_list'] = mScanTemplate('index', config('DEFAULT_MODULE'), 'MessageBoard');
$assign['title'] = trans('common.messageboard') . trans('common.add');
return view('admin.addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if (IS_POST) {
            $data       = $this->makeData();
            $resultEdit = Model\MessageBoard::mEdit($id, $data);
            if ($resultEdit) {
                $this->success(trans('common.messageboard') . trans('common.edit') . trans('common.success'),
                    route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('common.messageboard') . trans('common.edit') . trans('common.error'), $errorGoLink);
            }
        }

        $editInfo            = Model\MessageBoard::mFind($id);
        $editInfo['config']  = json_encode($editInfo['config']);
        $assign['edit_info'] = $editInfo;

        $assign['template_list'] = mScanTemplate('index', config('DEFAULT_MODULE'), 'MessageBoard');
        $assign['title']         = trans('common.messageboard') . trans('common.edit');
        return view('admin.addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\MessageBoard::mDel($id);
        if ($resultDel) {
            $this->success(trans('common.messageboard') . trans('common.del') . trans('common.success'),
                route('index'));
            return;
        } else {
            $this->error(trans('common.messageboard') . trans('common.del') . trans('common.error'), route('index'));
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
