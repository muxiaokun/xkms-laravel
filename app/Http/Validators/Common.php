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
        return preg_match('/^[\w\x80-\xff]+$/', $value) ? true : false;
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
        if (1 == $parameters[0] || '' != $value) {
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


    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 验证手机格式
     */
    public function phone($attribute, $value, $parameters, $validator)
    {
        preg_match('/^(1\d{10})$/', $value, $matches);
        return ($matches[1] == $value) ? true : false;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     * 短名格式
     */
    public function short_name($attribute, $value, $parameters, $validator)
    {
        preg_match('/^(\w*)$/', $value, $matches);
        return ($matches[1] == $value) ? true : false;
    }
}