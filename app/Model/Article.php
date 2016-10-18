<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Common
{
    use SoftDeletes;

    public static function mSelect($where = null, $page = false)
    {
        (new self)->mParseWhere($where);
        self::mGetPage($page);
        null !== self::option['order'] && self::order('is_stick desc,sort asc,update_time desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            (new self)->mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        !isset($data['add_time']) && $data['add_time'] = Carbon::now();
        return parent::mAdd($data);
    }

    protected function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        if (isset($where['attribute'])) {
            $attribute = [];
            foreach ($where['attribute'] as $attr) {
                $attr && $attribute[] = self::mMakeLikeArray($attr);
            }
            $where['attribute'] = $attribute;
            if (!$where['attribute']) {
                unset($where['attribute']);
            }
        }
    }

    protected function mEncodeData(&$data)
    {
        !isset($data['update_time']) && $data['update_time'] = Carbon::now();
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['content']) && $data['content'] = self::mEncodeContent($data['content']);
        if (isset($data['extend']) && is_array($data['extend'])) {
            $newExtend = [];
            foreach ($data['extend'] as $key => $value) {
                $newExtend[] = $key . ':' . $value;
            }
            $data['extend'] = '|' . implode('|', $newExtend) . '|';
        }
        if (isset($data['attribute']) && is_array($data['attribute'])) {
            $newAttribute = [];
            foreach ($data['attribute'] as $key => $value) {
                $newAttribute[] = $key . ':' . $value;
            }
            $data['attribute'] = '|' . implode('|', $newAttribute) . '|';
        }
        isset($data['album']) && $data['album'] = serialize($data['album']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        if (isset($data['extend']) && $data['extend']) {
            $data['extend'] = explode('|', substr($data['extend'], 1, strlen($data['extend']) - 2));
            $newExtend      = [];
            foreach ($data['extend'] as $valueStr) {
                list($key, $value) = explode(':', $valueStr);
                $newExtend[$key] = $value;
            }
            $data['extend'] = $newExtend;
        }
        if (isset($data['attribute']) && $data['attribute']) {
            $data['attribute'] = explode('|', substr($data['attribute'], 1, strlen($data['attribute']) - 2));
            $newAttribute      = [];
            foreach ($data['attribute'] as $valueStr) {
                list($key, $value) = explode(':', $valueStr);
                $newAttribute[$key] = $value;
            }
            $data['attribute'] = $newAttribute;
        }
        isset($data['album']) && $data['album'] = unserialize($data['album']);
    }
}
