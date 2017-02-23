<?php

namespace App\Model;


class MemberGroup extends Common
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
}
