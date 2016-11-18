<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Common
{
    use SoftDeletes;

    public function scopeMList($query, $where = null, $page = false)
    {
        if (!$query->getQuery()->orders) {
            $query->orderBy('is_stick', 'desc');
            $query->orderBy('sort', 'asc');
            $query->orderBy('update_at', 'desc');
        }
        return parent::scopeMList($query, $where, $page);
    }

    public function scopeMParseWhere($query, $where)
    {
        if (is_null($where)) {
            return;
        }

        if (isset($where['attribute'])) {
            $attribute = [];
            foreach ($where['attribute'] as $attr) {
                $attr && $attribute[] = $query->likeWhere($attr);
            }
            $where['attribute'] = $attribute;
            if (!$where['attribute']) {
                unset($where['attribute']);
            }
        }
    }

    public function scopeMEncodeData($query, $data)
    {
        !isset($data['update_time']) && $data['update_time'] = Carbon::now();
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['content']) && $data['content'] = $query->mEncodeContent($data['content']);
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

    public function scopeMDecodeData($query, $data)
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
