<?php

namespace App\Model;


use Illuminate\Support\Collection;

class AdminGroup extends Common
{
    protected $casts = [
        'privilege' => 'array',
    ];

    public function getManageIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setManageIdAttribute($value)
    {
        $this->attributes['manage_id'] = $this->transfixionEncode($value);
    }

    //查找出组权限
    public function scopeMFindPrivilege($query, $id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        $data = $query->where(['id' => $id, 'is_enable' => 1])->select(['privilege']);
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        $privilege = [];
        foreach ($data as $group) {
            $privilege = array_merge($privilege, $group['privilege']);
        }
        return new Collection($privilege);
    }

    //返回有权管理的组
    public function scopeMFindAllow($query)
    {
        $manageGroup = $query->select('id')
            ->where('manage_id', 'like', '%|' . session('backend_info.id') . '|%')
            ->pluck('id')
            ->push(0)
            ->toArray();
        return $manageGroup;
    }
}
