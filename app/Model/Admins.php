<?php

namespace App\Model;


use Carbon\Carbon;

class Admins extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mParseWhere($where);
        $query->mGetPage($page);
        null !== $query->options['order'] && $query->order('id desc');
        $data = $query->select(['*', 'login_ip as aip'])->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMAdd($query, $data)
    {
        if (!$data) {
            return false;
        }

        $data['add_time'] = Carbon::now();
        return $query->mAdd($data);
    }

    public function scopeMDel($query, $id)
    {
        //不能删除root用户
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return $query->mDel($id);
    }

    public function scopeMFind($query, $id)
    {
        $query->select(['*', 'login_ip as aip']);
        return parent::scopeMFind($query, $id);
        return $query->mFind($id);
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
            $adminInfo = $query->mFind($adminInfo['id']);
            return $adminInfo;
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
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        if ($data['admin_pwd']) {
            $randStr            = $query->_make_rand();
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
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        unset($data['admin_pwd']);
        unset($data['admin_rand']);
        isset($data['group_id']) && $data['group_id'] = explode('|',
            substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
        return $data;
    }
}
