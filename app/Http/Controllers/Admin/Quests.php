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
// 后台 问卷调查

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Quests extends Backend
{
    //列表
    public function index()
    {
        $where = array();

        //建立where
        $v_value                         = '';
        $v_value                         = I('title');
        $v_value && $where['title']      = array('like', '%' . $v_value . '%');
        $v_value                         = M_mktime_range('start_time');
        $v_value && $where['start_time'] = $v_value;
        $v_value                         = M_mktime_range('end_time');
        $v_value && $where['end_time']   = $v_value;

        $QuestsModel = D('Quests');
        $quests_list = $QuestsModel->mSelect($where, true);
        $this->assign('quests_list', $quests_list);
        $this->assign('quests_list_count', $QuestsModel->getPageCount($where));

        //初始化where_info
        $where_info               = array();
        $where_info['title']      = array('type' => 'input', 'name' => L('title'));
        $where_info['start_time'] = array('type' => 'time', 'name' => L('start') . L('time'));
        $where_info['end_time']   = array('type' => 'time', 'name' => L('end') . L('time'));
        $this->assign('where_info', $where_info);

        //初始化batch_handle
        $batch_handle                 = array();
        $batch_handle['add']          = $this->_check_privilege('add');
        $batch_handle['answer_index'] = $this->_check_privilege('index', 'QuestsAnswer');
        $batch_handle['answer_edit']  = $this->_check_privilege('edit', 'QuestsAnswer');
        $batch_handle['edit']         = $this->_check_privilege('edit');
        $batch_handle['del']          = $this->_check_privilege('del');
        $this->assign('batch_handle', $batch_handle);

        $this->assign('title', L('quests') . L('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $QuestsModel = D('Quests');
            $data        = $this->_make_data();
            $result_add  = $QuestsModel->mAdd($data);
            if ($result_add) {
                $this->success(L('quests') . L('add') . L('success'), U('Quests/index'));
                return;
            } else {
                $this->error(L('quests') . L('add') . L('error'), U('Quests/add'));
            }
        }
        $this->assign('title', L('add') . L('quests'));
        $this->display('addedit');
    }

    //编辑
    public function edit()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('index'));
        }

        $QuestsModel = D('Quests');
        if (IS_POST) {
            $data        = $this->_make_data();
            $result_edit = $QuestsModel->mEdit($id, $data);
            if ($result_edit) {
                $this->success(L('quests') . L('edit') . L('success'), U('Quests/index'));
                return;
            } else {
                $error_go_link = (is_array($id)) ? U('index') : U('edit', array('id' => $id));
                $this->error(L('quests') . L('edit') . L('error'), $error_go_link);
            }
        }
        $edit_info = $QuestsModel->mFind($id);
        $this->assign('edit_info', $edit_info);

        $this->assign('title', L('edit') . L('quests'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = I('id');
        if (!$id) {
            $this->error(L('id') . L('error'), U('Quests/index'));
        }

        $clear       = I('clear');
        $QuestsModel = D('Quests');
        if (!$clear) {
            $result_del = $QuestsModel->mDel($id);
        }

        if ($result_del || $clear) {
            //删除问卷会删除该问卷下的所有答案
            $QuestsAnswerModel = D('QuestsAnswer');
            //TODO 需要定义数据列
            $result_clear      = $QuestsAnswerModel->mClean($id);
            if ($clear) {
                if ($result_clear) {
                    $QuestsModel->where(array('id' => $id))->data(array('current_portion' => 0))->save();
                    $this->success(L('quests') . L('clear') . L('success'), U('Quests/index'));
                    return;
                } else {
                    $this->error(L('quests') . L('clear') . L('error'), U('Quests/index'));

                }
            }
            $this->success(L('quests') . L('del') . L('success'), U('Quests/index'));
            return;
        } else {
            $this->error(L('quests') . L('del') . L('error'), U('Quests/edit', array('id' => $id)));
        }
    }

    //构造数据
    private function _make_data()
    {
        $title         = I('title');
        $max_portion   = I('max_portion');
        $start_time    = I('start_time');
        $end_time      = I('end_time');
        $start_time    = M_mktime($start_time, true);
        $end_time      = M_mktime($end_time, true);
        $start_content = I('start_content');
        $end_content   = I('end_content');
        $access_info   = I('access_info');
        $ext_info      = I('ext_info');
        foreach ($ext_info as &$info) {
            $info = htmlspecialchars_decode($info);
            $info = json_decode($info, true);
        }
        $ext_info = json_encode($ext_info);

        $data                                                                       = array();
        ('add' == ACTION_NAME || null !== $title) && $data['title']                 = $title;
        ('add' == ACTION_NAME || null !== $max_portion) && $data['max_portion']     = $max_portion;
        ('add' == ACTION_NAME || null !== $start_time) && $data['start_time']       = $start_time;
        ('add' == ACTION_NAME || null !== $end_time) && $data['end_time']           = $end_time;
        ('add' == ACTION_NAME || null !== $start_content) && $data['start_content'] = $start_content;
        ('add' == ACTION_NAME || null !== $end_content) && $data['end_content']     = $end_content;
        ('add' == ACTION_NAME || null !== $access_info) && $data['access_info']     = $access_info;
        ('add' == ACTION_NAME || null !== $ext_info) && $data['ext_info']           = $ext_info;
        return $data;
    }
}
