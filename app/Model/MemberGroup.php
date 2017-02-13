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
        return $this->transfixionEncode($value);
    }

    //查找出组权限
    public function scopeMFindPrivilege($query, $id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        $data = $query->select(['is_enable', 'privilege'])->where(['id' => $id])->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        $privilege = [];
        foreach ($data as $group) {
            isset($group['is_enable']) && $privilege = array_merge($privilege, $group['privilege']);
        }
        return $privilege;
    }
}
