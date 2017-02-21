<?php
namespace App\Http\Validators;

use App\Model;

class Itlink
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 短名是否存在
     */
    public function itlink_name_exist($attribute, $value, $parameters, $validator)
    {
        $where   = [];
        $where[] = ['short_name', $value];
        $exist = Model\Itlink::where($where)->first();
        return $exist ? false : true;
    }
}