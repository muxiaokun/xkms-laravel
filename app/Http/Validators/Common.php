<?php

namespace App\Http\Validators;

class Common
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 验证用户名规则
     */
    public function user_name($attribute, $value, $parameters, $validator)
    {
        return preg_match('/^[a-zA-Z0-9_]+$/', $value) ? true : false;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 验证密码规则 可选是否强制验证
     */
    public function password($attribute, $value, $parameters, $validator)
    {
        if (isset($parameters[0]) && $parameters[0] || '' != $value) {
            return (6 <= strlen($value)) ? true : false;
        }
        return true;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 验证权限数据 不能超过自己有的
     */
    public function privilege($attribute, $value, $parameters, $validator)
    {
        if (!isset($parameters[0])) {
            return false;
        }
        $contrast = session($parameters[0] . '.privilege');
        if (in_array('all', $contrast)) {
            return true;
        }
        if (is_array($contrast)) {
            foreach ($value as $priv) {
                if (!in_array($priv, $contrast)) {
                    return false;
                }
            }
        }
        return true;
    }
}