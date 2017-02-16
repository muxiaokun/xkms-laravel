<?php

namespace App\Model;


class ArticleChannel extends Common
{
    protected $casts = [
        'extend' => 'array',
    ];

    public function getManageIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setManageIdAttribute($value)
    {
        $this->attributes['manage_id'] = $this->transfixionEncode($value);
    }

    public function getManageGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setManageGroupIdAttribute($value)
    {
        $this->attributes['manage_group_id'] = $this->transfixionEncode($value);
    }

    public function getAccessGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setAccessGroupIdAttribute($value)
    {
        $this->attributes['access_group_id'] = $this->transfixionEncode($value);
    }

    //返回有权管理的频道
    public function scopeMFindAllow($query, $type = true)
    {
        switch ($type) {
            case 'ma':
                $query->transfixionWhere('manage_id', [session('backend_info.id')]);
                break;
            case 'mg':
                $query->transfixionWhere('manage_group_id', session('backend_info.group_id'));
                break;
            default:
                $query->transfixionWhere('manage_id', [session('backend_info.id')]);
                $query->transfixionWhere('manage_group_id', session('backend_info.group_id'));
        }
        $mFindAllow = $query->select('id')->get()->pluck('id');
        $mFindAllow->push(0);
        return $mFindAllow;
    }
}
