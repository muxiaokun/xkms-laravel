<?php
namespace App\Http\Validators;

use App\Model;

class AdminGroup
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 管理员是否存在
     */
    public function admin_group_exist($attribute, $value, $parameters, $validator)
    {
        $where   = [];
        $where[] = ['name', $value];
        $data    = $validator->getData();
        if (isset($data['id'])) {
            $where[] = ['id', '!=', $data['id']];
        }
        $exist = Model\AdminGroups::where($where)->first();
        return $exist ? false : true;
    }
}