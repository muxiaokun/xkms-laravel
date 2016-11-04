<?php

namespace App\Model;


class MessageBoard extends Common
{
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
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['config']) && $data['config'] = unserialize($data['config']);
    }
}
