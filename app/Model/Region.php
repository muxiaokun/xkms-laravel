<?php

namespace App\Model;


class Region extends Common
{
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value ? $value : 0;
    }
}
