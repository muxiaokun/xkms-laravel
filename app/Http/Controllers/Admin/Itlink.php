<?php
// 后台 图文管理

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;
use Illuminate\Support\Facades\Validator;

class Itlink extends Backend
{
    //列表
    public function index()
    {
        //初始化翻页 和 列表数据
        $itlinkList            = Model\Itlink::where(function ($query) {
            $name = request('name');
            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }

            $short_name = request('short_name');
            if ($short_name) {
                $query->where('short_name', 'like', '%' . $short_name . '%');
            }

            $is_enable = request('is_enable');
            if ($is_enable) {
                $query->where('is_enable', '=', (1 == $is_enable) ? 1 : 0);
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['itlink_list'] = $itlinkList;

        //初始化where_info
        $whereInfo['name']       = ['type' => 'input', 'name' => trans('itlink.itlink') . trans('common.name')];
        $whereInfo['short_name'] = ['type' => 'input', 'name' => trans('common.short') . trans('common.name')];
        $whereInfo['is_enable']  = [
            'type'  => 'select',
            'name'  => trans('common.yes') . trans('common.no') . trans('common.enable'),
            'value' => [1 => trans('common.yes'), 2 => trans('common.no')],
        ];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['add']     = $this->_check_privilege('add');
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('itlink.itlink') . trans('common.management');
        return view('admin.Itlink_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Itlink::create($data);
            if ($resultAdd) {
                $this->addEditAfterCommon($data, $resultAdd->id);
                return $this->success(trans('itlink.itlink') . trans('common.add') . trans('common.success'),
                    route('Admin::Itlink::index'));
            } else {
                return $this->error(trans('itlink.itlink') . trans('common.add') . trans('common.error'),
                    route('Admin::Itlink::add'));
            }
        }

        $assign['edit_info'] = Model\Itlink::columnEmptyData();
        $assign['title']     = trans('itlink.itlink') . trans('common.add');
        return view('admin.Itlink_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Itlink::index'));
        }

        if (request()->isMethod('POST')) {
            $data = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = false;
            Model\Itlink::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                $this->addEditAfterCommon($data, $id);
                return $this->success(trans('itlink.itlink') . trans('common.edit') . trans('common.success'),
                    route('Admin::Itlink::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Itlink::index') : route('Admin::Itlink::edit',
                    ['id' => $id]);
                return $this->error(trans('itlink.itlink') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }

        $editInfo            = Model\Itlink::colWhere($id)->first()->toArray();
        $assign['edit_info'] = $editInfo;
        $assign['title']     = trans('itlink.itlink') . trans('common.edit');
        return view('admin.Itlink_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Itlink::index'));
        }

        $resultDel = Model\Itlink::destroy($id);
        if ($resultDel) {
            Model\ManageUpload::bindFile($id);
            return $this->success(trans('itlink.itlink') . trans('common.del') . trans('common.success'),
                route('Admin::Itlink::index'));
        } else {
            return $this->error(trans('itlink.itlink') . trans('common.del') . trans('common.error'),
                route('Admin::Itlink::index'));
        }
    }

    //异步和表单数据验证
    protected function doValidateForm($field, $data)
    {
        $result = ['status' => true, 'info' => ''];
        switch ($field) {
            case 'short_name':
                $validator = Validator::make($data, [
                    'short_name' => 'short_name|itlink_name_exist',
                ]);
                break;
        }

        if (isset($validator) && $validator->fails()) {
            $result['info'] = implode('', $validator->errors()->all());
        }

        if ($result['info']) {
            $result['status'] = false;
        }

        return $result;
    }

    //构造数据
    private function makeData($type)
    {
        //初始化参数
        $id           = request('id');
        $name         = request('name');
        $shortName    = request('short_name');
        $startTime    = request('start_time');
        $endTime      = request('end_time');
        $isEnable     = request('is_enable');
        $maxShowNum   = request('max_show_num');
        $maxHitNum    = request('max_hit_num');
        $showNum      = request('show_num');
        $hitNum       = request('hit_num');
        $inputExtInfo = request('ext_info');
        $extInfo      = [];
        if (is_array($inputExtInfo)) {
            foreach ($inputExtInfo['itl_link'] as $infoKey => $info) {
                $extInfo[] = [
                    'itl_link'   => htmlspecialchars_decode($info),
                    'itl_text'   => htmlspecialchars_decode($inputExtInfo['itl_text'][$infoKey]),
                    'itl_target' => htmlspecialchars_decode($inputExtInfo['itl_target'][$infoKey]),
                    'itl_image'  => htmlspecialchars_decode($inputExtInfo['itl_image'][$infoKey]),
                ];
            }
        }
        //检测初始化参数是否合法
        if ($id) {
            if (is_array($id)) {
                $errorGoLink = route('Admin::Itlink::index');
            } else {
                $errorGoLink = route('Admin::Itlink::edit', ['id' => $id]);
            }
        } else {
            $errorGoLink = route('Admin::Itlink::add');
        }

        $data = [];
        if ('add' == $type || null !== $name) {
            $data['name'] = $name;
        }
        if ('add' == $type || null !== $shortName) {
            $result = $this->doValidateForm('short_name', ['id' => $id, 'short_name' => $shortName]);
            if (!$result['status']) {
                return $this->error($result['info'], $errorGoLink);
            }
            $data['short_name'] = $shortName;
        }
        if (('add' == $type || null !== $startTime) && $startTime) {
            $data['start_time'] = $startTime;
        }
        if (('add' == $type || null !== $endTime) && $endTime) {
            $data['end_time'] = $endTime;
        }
        if ('add' == $type || null !== $isEnable) {
            $data['is_enable'] = $isEnable;
        }
        if ('add' == $type || null !== $maxShowNum) {
            $data['max_show_num'] = $maxShowNum ? $maxShowNum : 0;
        }
        if ('add' == $type || null !== $maxHitNum) {
            $data['max_hit_num'] = $maxHitNum ? $maxHitNum : 0;
        }
        if ('add' == $type || null !== $showNum) {
            $data['show_num'] = $showNum ? $showNum : 0;
        }
        if ('add' == $type || null !== $hitNum) {
            $data['hit_num'] = $hitNum ? $hitNum : 0;
        }
        if ('add' == $type || 0 < count($extInfo)) {
            $data['ext_info'] = $extInfo;
        }
        return $data;
    }

    //添加 编辑 之后 公共方法
    private function addEditAfterCommon(&$data, $id)
    {
        // 批量修改时不进行文件绑定
        if (is_array($id)) {
            return;
        }

        $bindFile = [];
        if (isset($data['ext_info']) && is_array($data['ext_info'])) {
            foreach ($data['ext_info'] as $item) {
                $bindFile[] = $item['itl_image'];
            }

        }
        Model\ManageUpload::bindFile($id, $bindFile);
    }
}
