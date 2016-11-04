<?php

namespace App\Model;


class MemberGroup extends Common
{
    //获得全部或者部分管理组列表
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mParseWhere($where);
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->orderBy('id', 'desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMDel($query, $id)
    {
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return $query->mDel($id);
    }

    //查找出组权限
    public function scopeMFindPrivilege($query, $id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        $data = $query->select('is_enable,privilege')->where(['id' => $id])->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        $privilege = [];
        foreach ($data as $group) {
            isset($group['is_enable']) && $privilege = array_merge($privilege, $group['privilege']);
        }
        return $privilege;
    }

    public function scopeMParseWhere($query, $where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id'] = $query->mMakeLikeArray($where['manage_id']);

    }

    //检查和格式化数据
    public function scopeMEncodeData($query, $data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        isset($data['manage_id']) && $data['manage_id'] = '|' . implode('|', array_unique($data['manage_id'])) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_template']) && $data['ext_template'] = serialize($data['ext_template']);
        return $data;
    }

    //检查和去除格式化数据
    public function scopeMDecodeData($query, $data)
    {
        isset($data['manage_id']) && $data['manage_id'] = explode('|',
            substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_template']) && $data['ext_template'] = unserialize($data['ext_template']);
        return $data;
    }
}
