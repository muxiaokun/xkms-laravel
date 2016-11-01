<?php

namespace App\Model;


class MessageBoardLog extends Common
{
    public static function mSelect($where = null, $page = false)
    {
        static::mGetPage($page);
        null !== static::option['order'] && static::order('add_time desc');
        $data = static::select('*,inet_ntoa(add_ip) as aip')->where($where)->select();
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
        $data['add_ip']   = request()->ip();
        return parent::mAdd($data);
    }

    public static function mFind($id)
    {
        static::select('*,inet_ntoa(add_ip) as aip');
        return parent::mFind($id);
    }

    public static function check_dont_submit($second)
    {
        $second = Carbon::now() - $second;
        $where  = $second . ' < add_time AND add_ip = "' . request()->ip() . '"';
        return (static::where($where)->count()) ? true : false;
    }

    protected function mEncodeData(&$data)
    {
        isset($data['send_info']) && $data['send_info'] = serialize($data['send_info']);
    }

    protected function mDecodeData(&$data)
    {
        isset($data['send_info']) && $data['send_info'] = unserialize($data['send_info']);
    }
}
