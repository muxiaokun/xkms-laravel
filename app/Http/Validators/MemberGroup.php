<?php
namespace App\Http\Validators;

use App\Model;

class MemberGroup
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 成员组是否存在
     */
    public function member_group_exist($attribute, $value, $parameters, $validator)
    {
        $where   = [];
        $where[] = ['name', $value];
        $data    = $validator->getData();
        if (isset($data['id'])) {
            $where[] = ['id', '!=', $data['id']];
        }
        $exist = Model\MemberGroup::where($where)->first();
        return $exist ? false : true;
    }
}