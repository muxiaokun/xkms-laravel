<?php

namespace App\Model;


use Carbon\Carbon;

class Admins extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        (new static)->mParseWhere($where);
        self::mGetPage($page);
        null !== self::options['order'] && self::order('id desc');
        $data = self::select('*,inet_ntoa(login_ip) as aip')->where($where)->select();
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

        $data['add_time'] = Carbon::now();
        return parent::mAdd($data);
    }

    public static function mDel($id)
    {
        //不能删除root用户
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::mDel($id);
    }

    public static function mFind($id)
    {
        self::select('*,inet_ntoa(login_ip) as aip');
        return parent::mFind($id);
    }

    public static function authorized($user, $pwd)
    {
        if (!$user) {
            return false;
        }

        $where     = [
            'admin_name' => $user,
            'is_enable'  => '1',
        ];
        $adminInfo = self::where($where)->first();
        if ($adminInfo['admin_pwd'] == md5($pwd . $adminInfo['admin_rand'])) {
            $data = [
                'last_time' => Carbon::now(),
                'login_ip'  => request()->ip(),
            ];
            self::where('id', '=', $adminInfo['id'])->update($data);
            $adminInfo = self::mFind($adminInfo['id']);
            return $adminInfo;
        } else {
            return false;
        }
    }

    protected function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['group_id']) && $where['group_id'] = self::mMakeLikeArray($where['group_id']);
    }

    protected function mEncodeData(&$data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        if ($data['admin_pwd']) {
            $randStr            = self::_make_rand();
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

    protected function mDecodeData(&$data)
    {
        unset($data['admin_pwd']);
        unset($data['admin_rand']);
        isset($data['group_id']) && $data['group_id'] = explode('|',
            substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
