<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Common
{
    use SoftDeletes;

    protected $casts = [
        'longText'  => 'array',
        'attribute' => 'array',
    ];

    protected $orders = [
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
        $this->attributes['access_group_id'] = $this->transfixionEncode($value);
    }


    public function getContentAttribute($value)
    {
        return mParseContent($value, true);
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = mParseContent($value);
    }

    public function setThumbAttribute($value)
    {
        $this->attributes['thumb'] = mParseUploadUrl($value);
    }

    public function getAlbumAttribute($value)
    {
        $value = json_decode($value, true);
        if (!is_array($value)) {
            return [];
        }
        foreach ($value as &$imageInfo) {
            $imageInfo['src'] = mMakeUploadUrl($imageInfo['src']);
        }
        return $value;
    }

    public function setAlbumAttribute($value)
    {
        foreach ($value as &$imageInfo) {
            $imageInfo        = json_decode(htmlspecialchars_decode($imageInfo), true);
            $imageInfo['src'] = mParseUploadUrl($imageInfo['src']);
        }
        $this->attributes['album'] = json_encode($value);
    }

    public function getExtendAttribute($value)
    {
        return $this->transfixionDecode($value, true);
    }

    public function setExtendAttribute($value)
    {
        $this->attributes['extend'] = $this->transfixionEncode($value, true);
    }

    public function getAttributeAttribute($value)
    {
        return $this->transfixionDecode($value, true);
    }

    public function setAttributeAttribute($value)
    {
        $this->attributes['attribute'] = $this->transfixionEncode($value, true);
    }

}
