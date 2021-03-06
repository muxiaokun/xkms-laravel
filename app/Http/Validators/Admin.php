<?php
namespace App\Http\Validators;

use App\Model;

class Admin
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 管理员是否存在
     */
    public function admin_exist($attribute, $value, $parameters, $validator)
    {
        $where   = [];
        $where[] = ['admin_name', $value];
        $data    = $validator->getData();
        if (isset($data['id'])) {
            $where[] = ['id', '!=', $data['id']];
        }
        $exist = Model\Admin::where($where)->first();
        return $exist ? false : true;
    }
}