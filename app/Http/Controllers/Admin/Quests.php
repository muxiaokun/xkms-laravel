<?php
// 后台 问卷调查

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Model;

class Quests extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('title');
        $whereValue && $where['title'] = ['like', '%' . $whereValue . '%'];
        $whereValue = mMktimeRange('start_time');
        $whereValue && $where[] = ['start_time', $whereValue];
        $whereValue = mMktimeRange('end_time');
        $whereValue && $where[] = ['end_time', $whereValue];

        $questsList = Model\Quests::where($where)->paginate(config('system.sys_max_row'))->appends(request()->all());
        $assign['quests_list'] = $questsList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['title']      = ['type' => 'input', 'name' => trans('common.title')];
        $whereInfo['start_time'] = ['type' => 'time', 'name' => trans('common.start') . trans('common.time')];
        $whereInfo['end_time']   = ['type' => 'time', 'name' => trans('common.end') . trans('common.time')];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle                 = [];
        $batchHandle['add']          = $this->_check_privilege('add');
        $batchHandle['answer_index'] = $this->_check_privilege('index', 'QuestsAnswer');
        $batchHandle['answer_edit']  = $this->_check_privilege('edit', 'QuestsAnswer');
        $batchHandle['edit']         = $this->_check_privilege('edit');
        $batchHandle['del']          = $this->_check_privilege('del');
        $assign['batch_handle']      = $batchHandle;

        $assign['title'] = trans('quests.quests') . trans('common.management');
        return view('admin.Quests_index', $assign);
    }

    //新增
    public function add()
    {
        if (request()->isMethod('POST')) {
            $data      = $this->makeData('add');
            if (!is_array($data)) {
                return $data;
            }

            $resultAdd = Model\Quests::create($data);
            if ($resultAdd) {
                return $this->success(trans('common.quests') . trans('common.add') . trans('common.success'),
                    route('Admin::Quests::index'));
            } else {
                return $this->error(trans('common.quests') . trans('common.add') . trans('common.error'),
                    route('Admin::Quests::add'));
            }
        }
        $assign['edit_info'] = Model\Quests::columnEmptyData();
        $assign['title']     = trans('common.add') . trans('common.quests');
        return view('admin.Quests_addedit', $assign);
    }

    //编辑
    public function edit()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('index'));
        }

        if (request()->isMethod('POST')) {
            $data       = $this->makeData('edit');
            if (!is_array($data)) {
                return $data;
            }

            $resultEdit = false;
            Model\Quests::colWhere($id)->get()->each(function ($item, $key) use ($data, &$resultEdit) {
                $resultEdit = $item->update($data);
                return $resultEdit;
            });
            if ($resultEdit) {
                return $this->success(trans('common.quests') . trans('common.edit') . trans('common.success'),
                    route('Admin::Quests::index'));
            } else {
                $errorGoLink = (is_array($id)) ? route('Admin::Quests::index') : route('Admin::Quests::edit',
                    ['id' => $id]);
                return $this->error(trans('common.quests') . trans('common.edit') . trans('common.error'),
                    $errorGoLink);
            }
        }
        $editInfo            = Model\Quests::colWhere($id)->first()->toArray();
        $assign['edit_info'] = $editInfo;

        $assign['title'] = trans('common.edit') . trans('common.quests');
        return view('admin.Quests_addedit', $assign);
    }

    //删除
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::Quests::index'));
        }

        $clear = request('clear');
        if (!$clear) {
            $resultDel = Model\Quests::destroy($id);
        }

        if ($resultDel || $clear) {
            //删除问卷会删除该问卷下的所有答案
            $resultClear = Model\QuestsAnswer::colWhere($id, 'quests_id')->delete();
            if ($clear) {
                if ($resultClear) {
                    Model\Quests::where(['id' => $id])->data(['current_portion' => 0])->save();
                    return $this->success(trans('common.quests') . trans('common.clear') . trans('common.success'),
                        route('Admin::Quests::index'));
                } else {
                    return $this->error(trans('common.quests') . trans('common.clear') . trans('common.error'),
                        route('Admin::Quests::index'));

                }
            }
            return $this->success(trans('common.quests') . trans('common.del') . trans('common.success'),
                route('Admin::Quests::index'));
        } else {
            return $this->error(trans('common.quests') . trans('common.del') . trans('common.error'),
                route('Admin::Quests::edit', ['id' => $id]));
        }
    }

    //构造数据
    private function makeData($type)
    {
        $title        = request('title');
        $maxPortion   = request('max_portion');
        $startTime    = request('start_time');
        $endTime      = request('end_time');
        $startTime    = mMktime($startTime, true);
        $endTime      = mMktime($endTime, true);
        $startContent = request('start_content');
        $endContent   = request('end_content');
        $accessInfo   = request('access_info');
        $extInfo      = request('ext_info');
        foreach ($extInfo as &$info) {
            $info = htmlspecialchars_decode($info);
            $info = json_decode($info, true);
        }

        $data = [];
        if ('add' == $type || null !== $title) {
            $data['title'] = $title;
        }
        if ('add' == $type || null !== $maxPortion) {
            $data['max_portion'] = $maxPortion;
        }
        if ('add' == $type || null !== $startTime) {
            $data['start_time'] = $startTime;
        }
        if ('add' == $type || null !== $endTime) {
            $data['end_time'] = $endTime;
        }
        if ('add' == $type || null !== $startContent) {
            $data['start_content'] = $startContent;
        }
        if ('add' == $type || null !== $endContent) {
            $data['end_content'] = $endContent;
        }
        if ('add' == $type || null !== $accessInfo) {
            $data['access_info'] = $accessInfo;
        }
        if ('add' == $type || null !== $extInfo) {
            $data['ext_info'] = $extInfo;
        }
        return $data;
    }
}
