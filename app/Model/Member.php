<?php

namespace App\Model;


class Member extends Common
{
    protected $casts = [
        'privilege' => 'array',
    ];

    public function setMemberPwdAttribute($value)
    {
        if ($value) {
            $randStr                         = mRandStr('pr');
            $password                        = md5($value . $randStr);
            $this->attributes['member_pwd']  = $password;
            $this->attributes['member_rand'] = $randStr;
        }
    }

    public function getGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setGroupIdAttribute($value)
    {
        $this->attributes['group_id'] = $this->transfixionEncode($value);
    }
}
