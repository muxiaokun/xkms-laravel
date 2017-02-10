<?php

namespace App\Model;


use Carbon\Carbon;

class Admins extends Common
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
        sort($value);
        $this->attributes['group_id'] = $this->transfixionEncode($value);
    }

    public function scopeAuthorized($query, $user, $pwd)
    {
        if (!$user) {
            return false;
        }

        $where     = [
            'admin_name' => $user,
            'is_enable'  => '1',
        ];
        $adminInfo = $query->where($where)->first();
        if ($adminInfo['admin_pwd'] == md5($pwd . $adminInfo['admin_rand'])) {
            $data = [
                'last_time' => Carbon::now(),
                'login_ip'  => request()->ip(),
            ];
            $query->where('id', '=', $adminInfo['id'])->update($data);
            $adminInfo = $query->where('id', $adminInfo['id'])->first();
            return $adminInfo;
        } else {
            return false;
        }
    }
}
