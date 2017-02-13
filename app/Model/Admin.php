<?php

namespace App\Model;

class Admin extends Common
{
    protected $casts = [
        'privilege' => 'array',
    ];

    public function setAdminPwdAttribute($value)
    {
        if ($value) {
            $randStr                        = mRandStr('pr');
            $password                       = md5($value . $randStr);
            $this->attributes['admin_pwd']  = $password;
            $this->attributes['admin_rand'] = $randStr;
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
