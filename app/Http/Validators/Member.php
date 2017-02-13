<?php
namespace App\Http\Validators;

use App\Model;

class Member
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 管理员是否存在
     */
    public function member_exist($attribute, $value, $parameters, $validator)
    {
        $where   = [];
        $where[] = ['member_name', $value];
        $data    = $validator->getData();
        if (isset($data['id'])) {
            $where[] = ['id', '!=', $data['id']];
        }
        $exist = Model\Member::where($where)->first();
        return $exist ? false : true;
    }
}