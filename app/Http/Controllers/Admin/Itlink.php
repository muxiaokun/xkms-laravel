<?php
// 后台 图文管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Itlink extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $whereValue = '';
        $whereValue = request('name');
        $whereValue && $where['name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('short_name');
        $whereValue && $where['short_name'] = ['like', '%' . $whereValue . '%'];
        $whereValue = request('is_enable');
        $whereValue && $where['is_enable'] = (1 == $whereValue) ? 1 : 0;
        $whereValue = request('is_statistics');
        $whereValue && $where['is_statistics'] = (1 == $whereValue) ? 1 : 0;

        //初始化翻页 和 列表数据
        $itlinkList                  = Model\Itlink::mSelect($where, true);
        $assign['itlink_list']       = $itlinkList;
        $assign['itlink_list_count'] = Model\Itlink::mGetPageCount($where);

        //初始化where_info
        $whereInfo['name']          = ['type' => 'input', 'name' => trans('common.itlink') . trans('common.name')];
        $whereInfo['short_name']    = ['type' => 'input', 'name' => trans('common.short') . trans('common.name')];
        $whereInfo['is_enable']     = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
            'value' => [1 => trans('common.yes'), 2 => trans('common.no')],
        ];
        $whereInfo['is_statistics'] = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.statistics'),
            'value' => [1 => trans('common.yes'), 2 => trans('common.no')],
        ];
        $assign['where_info']       = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.itlink') . trans('common.management');
        return view('admin.', $assign);
    }

    //新增
    public function add()
    {
        if (IS_POST) {
            $data      = $this->makeData();
            $resultAdd = Model\Itlink::mAdd($data);
            if ($resultAdd) {
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('common.itlink') . trans('common.add') . trans('common.success'), route('index'));
                return;
            } else {
                $this->error(trans('common.itlink') . trans('common.add') . trans('common.error'), route('add'));
            }
        }

        $assign['title'] = trans('common.itlink') . trans('common.add');
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
            $resultEdit = Model\Itlink::mEdit($id, $data);
            if ($resultEdit) {
                $this->addEditAfterCommon($data, $id);
                $this->success(trans('common.itlink') . trans('common.edit') . trans('common.success'), route('index'));
                return;
            } else {
                $errorGoLink = (is_array($id)) ? route('index') : U('edit', ['id' => $id]);
                $this->error(trans('common.itlink') . trans('common.edit') . trans('common.error'), $errorGoLink);
            }
        }

        $editInfo            = Model\Itlink::mFind($id);
        $assign['edit_info'] = $editInfo;
        $assign['title']     = trans('common.itlink') . trans('common.edit');
        return view('admin.addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        $resultDel = Model\Itlink::mDel($id);
        if ($resultDel) {
            Model\ManageUpload::mEdit($id);
            $this->success(trans('common.itlink') . trans('common.del') . trans('common.success'), route('index'));
            return;
        } else {
            $this->error(trans('common.itlink') . trans('common.del') . trans('common.error'), route('index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'short_name':
                //检查用户名是否存在
                $itlinkInfo = Model\Itlink::mSelect([
                    'short_name' => $data['short_name'],
                    'id'         => ['neq', $data['id']],
                ]);
                if (0 < count($itlinkInfo)) {
                    $result['info'] = trans('common.short') . trans('common.name') . trans('common.exists');
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
        $id           = request('id');
        $name         = request('name');
        $shortName    = request('short_name');
        $startTime    = request('start_time');
        $endTime      = request('end_time');
        $startTime    = mMktime($startTime, true);
        $endTime      = mMktime($endTime, true);
        $isEnable     = request('is_enable');
        $isStatistics = request('is_statistics');
        $maxShowNum   = request('max_show_num');
        $maxHitNum    = request('max_hit_num');
        $showNum      = request('show_num');
        $hitNum       = request('hit_num');
        $inputExtInfo = request('ext_info');
        $extInfo      = [];
        foreach ($inputExtInfo['itl_link'] as $infoKey => $info) {
            $extInfo[] = [
                'itl_link'   => htmlspecialchars_decode($info),
                'itl_text'   => htmlspecialchars_decode($inputExtInfo['itl_text'][$infoKey]),
                'itl_target' => htmlspecialchars_decode($inputExtInfo['itl_target'][$infoKey]),
                'itl_image'  => htmlspecialchars_decode($inputExtInfo['itl_image'][$infoKey]),
            ];
        }

        //检测初始化参数是否合法
        $errorGoLink = (!$id) ? route('add') : (is_array($id)) ? U('index') : U('edit', ['id' => $id]);
        if ('add' == ACTION_NAME || null !== $shortName) {
            $result = $this->doValidateForm('short_name', ['id' => $id, 'short_name' => $shortName]);
            if (!$result['status']) {
                $this->error($result['info'], $errorGoLink);
            }

        }

        $data = [];
        ('add' == ACTION_NAME || null !== $name) && $data['name'] = $name;
        ('add' == ACTION_NAME || null !== $shortName) && $data['short_name'] = $shortName;
        ('add' == ACTION_NAME || null !== $startTime) && $data['start_time'] = $startTime;
        ('add' == ACTION_NAME || null !== $endTime) && $data['end_time'] = $endTime;
        ('add' == ACTION_NAME || null !== $isEnable) && $data['is_enable'] = $isEnable;
        ('add' == ACTION_NAME || null !== $isStatistics) && $data['is_statistics'] = $isStatistics;
        ('add' == ACTION_NAME || null !== $maxShowNum) && $data['max_show_num'] = $maxShowNum;
        ('add' == ACTION_NAME || null !== $maxHitNum) && $data['max_hit_num'] = $maxHitNum;
        ('add' == ACTION_NAME || null !== $showNum) && $data['show_num'] = $showNum;
        ('add' == ACTION_NAME || null !== $hitNum) && $data['hit_num'] = $hitNum;
        ('add' == ACTION_NAME || 0 < count($extInfo)) && $data['ext_info'] = $extInfo;
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function addEditAfterCommon(&$data, $id)
    {
        // 批量修改时不进行文件绑定
        if (is_array($id)) {
            return;
        }

        $bindFile          = [];
        foreach ($data as $item) {
            $bindFile[] = $item['link_image'];
        }
        Model\ManageUpload::mEdit($id, $bindFile);
    }
}
