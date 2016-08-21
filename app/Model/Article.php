<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Common
{
    use SoftDeletes;

    public function mSelect($where = null, $page = false)
    {
        $this->parseWhere($where);
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('is_stick desc,sort asc,update_time desc');
        $data = $this->where($where)->select();
        foreach ($data as &$data_row) {$this->decodeData($data_row);}
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        !isset($data['add_time']) && $data['add_time'] = time();
        return parent::mAdd($data);
    }

    protected function parseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        if (isset($where['attribute'])) {
            $attribute = array();
            foreach ($where['attribute'] as $attr) {
                $attr && $attribute[] = $this->_make_like_arr($attr);
            }
            $where['attribute'] = $attribute;
            if (!$where['attribute']) {
                unset($where['attribute']);
            }
        }
    }

    protected function encodeData(&$data)
    {
        !isset($data['update_time']) && $data['update_time']        = time();
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['content']) && $data['content']                 = $this->_encode_content($data['content']);
        if (isset($data['extend']) && is_array($data['extend'])) {
            $new_extend = array();
            foreach ($data['extend'] as $key => $value) {
                $new_extend[] = $key . ':' . $value;
            }
            $data['extend'] = '|' . implode('|', $new_extend) . '|';
        }
        if (isset($data['attribute']) && is_array($data['attribute'])) {
            $new_attribute = array();
            foreach ($data['attribute'] as $key => $value) {
                $new_attribute[] = $key . ':' . $value;
            }
            $data['attribute'] = '|' . implode('|', $new_attribute) . '|';
        }
        isset($data['album']) && $data['album'] = serialize($data['album']);
    }

    protected function decodeData(&$data)
    {
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        if (isset($data['extend']) && $data['extend']) {
            $data['extend'] = explode('|', substr($data['extend'], 1, strlen($data['extend']) - 2));
            $new_extend     = array();
            foreach ($data['extend'] as $value_str) {
                list($key, $value) = explode(':', $value_str);
                $new_extend[$key]  = $value;
            }
            $data['extend'] = $new_extend;
        }
        if (isset($data['attribute']) && $data['attribute']) {
            $data['attribute'] = explode('|', substr($data['attribute'], 1, strlen($data['attribute']) - 2));
            $new_attribute     = array();
            foreach ($data['attribute'] as $value_str) {
                list($key, $value)   = explode(':', $value_str);
                $new_attribute[$key] = $value;
            }
            $data['attribute'] = $new_attribute;
        }
        isset($data['album']) && $data['album'] = unserialize($data['album']);
    }
}
