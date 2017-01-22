<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Common
{
    use SoftDeletes;

    protected $casts = [
        'longText'  => 'array',
        'attribute' => 'array',
        'album'     => 'array',
    ];

    public $orders = [
        'id'        => 'desc',
        'sort'      => 'asc',
        'update_at' => 'desc',
    ];

    public function getAccessGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setAccessGroupIdAttribute($value)
    {
        return $this->transfixionEncode($value);
    }


    public function scopeMEncodeData($query, $data)
    {
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
    }

    public function scopeMDecodeData($query, $data)
    {
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
    }
}
