<?php
// 后台 图文管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;

class Itlink extends Backend
{
    //列表
    public function index()
    {
        $ItlinkModel = D('Itlink');
        //建立where
        $whereValue                            = '';
        $whereValue                            = request('name');
        $whereValue && $where['name']          = array('like', '%' . $whereValue . '%');
        $whereValue                            = request('short_name');
        $whereValue && $where['short_name']    = array('like', '%' . $whereValue . '%');
        $whereValue                            = request('is_enable');
        $whereValue && $where['is_enable']     = (1 == $whereValue) ? 1 : 0;
        $whereValue                            = request('is_statistics');
        $whereValue && $where['is_statistics'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $itlinkList = $ItlinkModel->mSelect($where, true);
        $this->assign('itlink_list', $itlinkList);
        $this->assign('itlink_list_count', $ItlinkModel->mGetPageCount($where));

        //初始化where_info
        $whereInfo['name']          = array('type' => 'input', 'name' => trans('itlink') . trans('name'));
        $whereInfo['short_name']    = array('type' => 'input', 'name' => trans('short') . trans('name'));
        $whereInfo['is_enable']     = array('type' => 'select', 'name' => trans('yes') . trans('no') . trans('enable'), 'value' => array(1 => trans('yes'), 2 => trans('no')));
        $whereInfo['is_statistics'] = array('type' => 'select', 'name' => trans('yes') . trans('no') . trans('statistics'), 'value' => array(1 => trans('yes'), 2 => trans('no')));
        $this->assign('where_info', $whereInfo);

        //初始化batch_handle
        $batchHandle         = array();
        $batchHandle['add']  = $this->_check_privilege('add');
        $batchHandle['edit'] = $this->_check_privilege('edit');
        $batchHandle['del']  = $this->_check_privilege('del');
        $this->assign('batch_handle', $batchHandle);

        $this->assign('title', trans('itlink') . trans('management'));
        $this->display();
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $ItlinkModel = D('Itlink');
            $data        = $this->makeData();
            $resultAdd  = $ItlinkModel->mAdd($data);
            if ($resultAdd) {
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('itlink') . trans('add') . trans('success'), route('index'));
                return;
            } else {
                $this->error(trans('itlink') . trans('add') . trans('error'), route('add'));
            }
        }

        $this->assign('title', trans('itlink') . trans('add'));
        $this->display('addedit');
    }
    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ItlinkModel = D('Itlink');
        if (IS_POST) {
            $data        = $this->makeData();
            $resultEdit = $ItlinkModel->mEdit($id, $data);
            if ($resultEdit) {
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('itlink') . trans('edit') . trans('success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', array('id' => $id));
                $this->error(trans('itlink') . trans('edit') . trans('error'), $errorGoLink);
            }
        }

        $editInfo = $ItlinkModel->mFind($id);
        $this->assign('edit_info', $editInfo);
        $this->assign('title', trans('itlink') . trans('edit'));
        $this->display('addedit');
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('id') . trans('error'), route('index'));
        }

        $ItlinkModel = D('Itlink');
        $resultDel  = $ItlinkModel->mDel($id);
        if ($resultDel) {
            $ManageUploadModel = D('ManageUpload');
            $ManageUploadModel->mEdit($id);
            $this->success(trans('itlink') . trans('del') . trans('success'), route('index'));
            return;
        } else {
            $this->error(trans('itlink') . trans('del') . trans('error'), route('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = array('status' => true, 'info' => '');
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $ItlinkModel = D('Itlink');
                $itlinkInfo = $ItlinkModel->mSelect(array('short_name' => $data['short_name'], 'id' => array('neq', $data['id'])));
                if (0 < count($itlinkInfo)) {
                    $result['info'] = trans('short') . trans('name') . trans('exists');
                    break;
                }
                break;
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //构造数据
    private function makeData()
    {
        //初始化参数
        $id             = request('id');
        $name           = request('name');
        $shortName     = request('short_name');
        $startTime     = request('start_time');
        $endTime       = request('end_time');
        $startTime     = mMktime($startTime, true);
        $endTime       = mMktime($endTime, true);
        $isEnable      = request('is_enable');
        $isStatistics  = request('is_statistics');
        $maxShowNum   = request('max_show_num');
        $maxHitNum    = request('max_hit_num');
        $showNum       = request('show_num');
        $hitNum        = request('hit_num');
        $inputExtInfo = request('ext_info');
        $extInfo       = array();
        foreach ($inputExtInfo['itl_link'] as $infoKey => $info) {
            $extInfo[] = array(
                'itl_link'   => htmlspecialchars_decode($info),
                'itl_text'   => htmlspecialchars_decode($inputExtInfo['itl_text'][$infoKey]),
                'itl_target' => htmlspecialchars_decode($inputExtInfo['itl_target'][$infoKey]),
                'itl_image'  => htmlspecialchars_decode($inputExtInfo['itl_image'][$infoKey]),
            );
        }

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', array('id' => $id));
        if ('add' == ACTION_NAME || null !== $shortName) {
            $result = $this->doValidateForm('short_name', array('id' => $id, 'short_name' => $shortName));
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data                                                                       = array();
        ('add' == ACTION_NAME || null !== $name) && $data['name']                   = $name;
        ('add' == ACTION_NAME || null !== $shortName) && $data['short_name']       = $shortName;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time']       = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time']           = $endTime;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable']         = $isEnable;
        ('add' == ACTION_NAME || null !== $isStatistics) && $data['is_statistics'] = $isStatistics;
        ('add' == ACTION_NAME || null !== $maxShowNum) && $data['max_show_num']   = $maxShowNum;
        ('add' == ACTION_NAME || null !== $maxHitNum) && $data['max_hit_num']     = $maxHitNum;
        ('add' == ACTION_NAME || null !== $showNum) && $data['show_num']           = $showNum;
        ('add' == ACTION_NAME || null !== $hitNum) && $data['hit_num']             = $hitNum;
        ('add' == ACTION_NAME || 0 < count($extInfo)) && $data['ext_info']         = $extInfo;
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function addEditAfterCommon(&$data, $id)
    {
        // 批量修改时不进行文件绑定
        if (is_array($id)) {
            return;
        }

        $ManageUploadModel = D('ManageUpload');
        $bindFile         = array();
        foreach ($data as $item) {
            $bindFile[] = $item['link_image'];
        }
        $ManageUploadModel->mEdit($id, $bindFile);
    }
}
