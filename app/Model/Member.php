<?php

namespace App\Model;


class Member extends Common
{
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

    public function scopeMParseWhere($query, $where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['group_id']) && $where['group_id'] = $query->mMakeLikeArray($where['group_id']);
    }

    public function scopeMEncodeData($query, $data)
    {
        if ($data['member_pwd']) {
            $randStr             = $query->_make_rand();
            $data['member_pwd']  = md5($data['member_pwd'] . $randStr);
            $data['member_rand'] = $randStr;
        } else {
            unset($data['member_pwd']);
            unset($data['member_rand']);
        }
        !isset($data['group_id']) && $data['group_id'] = [1];
        //组合权限
        isset($data['group_id']) && $data['group_id'] = '|' . implode('|', $data['group_id']) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    public function scopeMDecodeData($query, $data)
    {
        unset($data['member_pwd']);
        unset($data['member_rand']);
        isset($data['group_id']) && $data['group_id'] = explode('|',
            substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
