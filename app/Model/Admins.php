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
        $randStr                        = mRandStr('pr');
        $password                       = md5($value . $randStr);
        $this->attributes['admin_rand'] = $randStr;
        return $password;
    }

    public function getGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setGroupIdAttribute($value)
    {
        return $this->transfixionEncode($value);
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

    public function scopeMEncodeData($query, $data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        if ($data['admin_pwd']) {
            $randStr            = mRandStr('pr');
            $data['admin_pwd']  = md5($data['admin_pwd'] . $randStr);
            $data['admin_rand'] = $randStr;
        } else {
            unset($data['admin_pwd']);
            unset($data['admin_rand']);
        }
    }

    public function scopeMDecodeData($query, $data)
    {
        unset($data['admin_pwd']);
        unset($data['admin_rand']);
    }
}
