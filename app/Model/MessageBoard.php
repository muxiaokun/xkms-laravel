<?php

namespace App\Model;


class MessageBoard extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        static::mGetPage($page);
        null !== static::option['order'] && static::order('id desc');
        $data = static::where($where)->select();
        foreach ($data as &$dataRow) {
            (new static)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mDel($id)
    {
        //不能删除默认的留言板
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::mDel($id);
    }

    protected function mEncodeData(&$data)
    {
        isset($data['config']) && $data['config'] = serialize($data['config']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['config']) && $data['config'] = unserialize($data['config']);
    }
}
