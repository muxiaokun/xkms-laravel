<?php

namespace App\Model;


use Illuminate\Support\Collection;

class AdminGroups extends Common
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
        $where       = [
            'manage_id' => session('backend_info.id'),
        ];
        $manageGroup = $query->select('id')->where($where)->get();
        $mFindAllow  = [];
        foreach ($manageGroup as $group) {
            $mFindAllow[] = $group['id'];
        }
        //不归组的任何人都可以管理
        $mFindAllow[] = 0;
        return $mFindAllow;
    }

    //检查和格式化数据
    public function scopeMEncodeData($query, $data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
    }
}
