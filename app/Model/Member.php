<?php

namespace App\Model;


class Member extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        (new static)->mParseWhere($where);
        static::mGetPage($page);
        null !== static::option['order'] && static::order('id desc');
        $data = static::select('*,inet_ntoa(login_ip) as aip')->where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['register_time'] = Carbon::now();
        return parent::mAdd($data);
    }

    public static function mFind($id)
    {
        static::select('*,inet_ntoa(login_ip) as aip');
        return parent::mFind($id);
    }

    public static function authorized($user, $pwd, $memberId)
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
        $memberInfo = static::where($where)->first();
        if ($memberInfo['member_pwd'] == md5($pwd . $memberInfo['member_rand']) || $memberId) {
            $data = [
                'last_time' => Carbon::now(),
                'login_ip'  => request()->ip(),
            ];
            static::where(['id' => $memberInfo['id']])->data($data)->save();
            $memberInfo = static::mFind($memberInfo['id']);
            return $memberInfo;
        } else {
            return false;
        }
    }

    protected function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['group_id']) && $where['group_id'] = static::mMakeLikeArray($where['group_id']);
    }

    protected function mEncodeData(&$data)
    {
        if ($data['member_pwd']) {
            $randStr             = static::_make_rand();
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

    protected function mDecodeData(&$data)
    {
        unset($data['member_pwd']);
        unset($data['member_rand']);
        isset($data['group_id']) && $data['group_id'] = explode('|',
            substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
