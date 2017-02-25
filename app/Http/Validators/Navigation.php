<?php
namespace App\Http\Validators;

use App\Model;

class Navigation
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 短名是否存在
     */
    public function navigation_name_exist($attribute, $value, $parameters, $validator)
    {
        $where   = [];
        $where[] = ['short_name', $value];
        $data = $validator->getData();
        if (isset($data['id'])) {
            $where[] = ['id', '!=', $data['id']];
        }
        $exist = Model\Navigation::where($where)->first();
        return $exist ? false : true;
    }
}