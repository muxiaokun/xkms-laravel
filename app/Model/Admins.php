<?php

namespace App\Model;


use Carbon\Carbon;

class Admins extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->select(['*', 'login_ip as aip']);
        return parent::scopeMList($query, $where, $page);
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
        //组合权限
        isset($data['group_id']) && $data['group_id'] = '|' . implode('|', $data['group_id']) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    public function scopeMDecodeData($query, $data)
    {
        unset($data['admin_pwd']);
        unset($data['admin_rand']);
        isset($data['group_id']) && $data['group_id'] = explode('|',
            substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
