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

    public function authorized($query, $user, $pwd, $memberId)
    {
        if (!$user && !$memberId) {
            return false;
        }

        if ($memberId) {
            $where = [
                'id' => $memberId,
            ];
        } else {
            $where = [
                'member_name' => $user,
                'is_enable'   => '1',
            ];
        }
        $memberInfo = $query->where($where)->first();
        if ($memberInfo['member_pwd'] == md5($pwd . $memberInfo['member_rand']) || $memberId) {
            $data = [
                'last_time' => Carbon::now(),
                'login_ip'  => request()->ip(),
            ];
            $query->where(['id' => $memberInfo['id']])->data($data)->save();
            $memberInfo = $query->where('id', $memberInfo['id'])->first();
            return $memberInfo;
        } else {
            return false;
        }
    }
}
