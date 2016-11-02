<?php

namespace App\Model;


class MessageBoard extends Common
{
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mGetPage($page);
        null !== $query->option['order'] && $query->order('id desc');
        $data = $query->where($where)->select();
        foreach ($data as &$dataRow) {
            $query->mDecodeData($dataRow);
        }
        return $data;
    }

    public function scopeMDel($query, $id)
    {
        //不能删除默认的留言板
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return $query->mDel($id);
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['config']) && $data['config'] = serialize($data['config']);
        return $data;
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['config']) && $data['config'] = unserialize($data['config']);
        return $data;
    }
}
