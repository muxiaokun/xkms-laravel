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
        $privilege = [];
        $query->whereIn('id', $id)
            ->where('is_enable', 1)
            ->select(['privilege'])
            ->get()->each(function ($item, $key) use (&$privilege) {
                $privilege = array_merge($privilege, $item['privilege']);
            });
        return collect($privilege);
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
