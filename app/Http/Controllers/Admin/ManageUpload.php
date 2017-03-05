<?php
// 后台 管理上传

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Backend;
use App\Http\Controllers\CommonManageUpload;
use App\Model;
use Illuminate\Filesystem\Filesystem;

class ManageUpload extends Backend
{
    //列表
    public function index()
    {
        //建立where
        $where      = [];
        $whereValue = request('suffix');
        $whereValue && $where[] = ['suffix', $whereValue];
        $whereValue = request('bind_info');
        $whereValue && $where['bind_info'] = ['like', '%|' . $whereValue . ':%'];
        $whereValue = mMktimeRange('created_at');
        $whereValue && $where[] = ['created_at', $whereValue];

        //初始化翻页 和 列表数据
        $manageUploadList = Model\ManageUpload::where(function ($query) {
            $created_at = mMktimeRange('created_at');
            if ($created_at) {
                $query->timeWhere('created_at', $created_at);
            }

            $suffix = request('suffix');
            if ($suffix) {
                $query->where('suffix', 'like', '%' . $suffix . '%');
            }

            $bind_info = request('bind_info');
            if ($bind_info) {
                $query->where('bind_info', 'like', '%|' . $bind_info . ':%');
            }

        })->paginate(config('system.sys_max_row'))->appends(request()->all());
        foreach ($manageUploadList as &$manageUpload) {
            switch ($manageUpload['user_type']) {
                case 1:
                    $manageUpload['user_name'] = Model\Admin::colWhere($manageUpload['user_id'])->get()->implode('admin_name',
                        ' | ');
                    break;
                case 2:
                    $manageUpload['user_name'] = Model\Member::colWhere($manageUpload['user_id'])->get()->implode('member_name',
                        ' | ');
                    break;
            }
        }
        $assign['manage_upload_list'] = $manageUploadList;

        //初始化where_info
        $whereInfo               = [];
        $whereInfo['created_at'] = ['type' => 'time', 'name' => trans('common.add') . trans('common.time')];
        $whereInfo['suffix']     = ['type' => 'input', 'name' => trans('common.suffix')];
        $whereInfo['bind_info']  = ['type' => 'input', 'name' => trans('common.bind') . trans('common.info')];
        $assign['where_info']    = $whereInfo;

        //初始化batch_handle
        $batchHandle            = [];
        $batchHandle['edit']    = $this->_check_privilege('edit');
        $batchHandle['del']     = $this->_check_privilege('del');
        $assign['batch_handle'] = $batchHandle;

        $assign['title'] = trans('common.file') . trans('common.management');
        return view('admin.ManageUpload_index', $assign);
    }

    //删除图片
    public function del()
    {
        $id = request('id');
        if (!$id) {
            return $this->error(trans('common.id') . trans('common.error'), route('Admin::ManageUpload::index'));
        }
        $filesystem = new Filesystem();
        $resultDel  = false;
        Model\ManageUpload::colWhere($id)->get()->each(function ($item, $key) use ($filesystem, &$resultDel) {
            $path = storage_path('app/public/' . $item->path);
            if ($filesystem->delete($path)) {
                $resultDel = $item->delete();
            }

        });
        if ($resultDel) {
            return $this->success(trans('common.file') . trans('common.del') . trans('common.success'),
                route('Admin::ManageUpload::index'));
        } else {
            return $this->error(trans('common.file') . trans('common.del') . trans('common.error'),
                route('Admin::ManageUpload::index'));
        }
    }

    //清除未用
    public function edit()
    {
        $lang = trans('common.yes') . trans('common.no') . trans('common.confirm') . trans('common.clear');
        if (!$this->showConfirm($lang)) {
            return;
        }

        $filesystem = new Filesystem();
        $resultDel  = false;
        Model\ManageUpload::whereNull('bind_info')->orWhere('bind_info', '=', '')->get()
            ->each(function ($item, $key) use ($filesystem, &$resultDel) {
                $path = storage_path('app/public/' . $item->path);
                if ($filesystem->delete($path)) {
                    $resultDel = $item->delete();
                }

            });

        if ($resultDel) {
            return $this->success(trans('common.clear') . trans('common.file') . trans('common.success'),
                route('Admin::ManageUpload::index'));
        } else {
            return $this->error(trans('common.clear') . trans('common.file') . trans('common.error'),
                route('Admin::ManageUpload::index'));
        }
    }
}
