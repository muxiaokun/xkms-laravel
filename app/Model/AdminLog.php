<?php

namespace App\Model;


class AdminLog extends Common
{
    //查询日志
    public static function mSelect($where = null, $page = false)
    {
        self::mGetPage($page);
        null !== self::options['order'] && self::order('add_time desc');
        return self::where($where)->select();
    }

    //添加日志 管理员编号 信息为空为传参 操作的模型
    public static function mAdd($adminId, $modelName = false, $msg = false)
    {
        if (!$adminId) {
            return false;
        }

        if (!$modelName) {
            $modelName = "SYS_AUTO_LOG";
        }

        if (!$msg) {
            $denyLogRequest = C('SYS_DENY_LOG_REQUEST');
            $request        = I('request.');
            foreach ($request as $key => $value) {
                if (in_array($key, $denyLogRequest)) {
                    unset($request[$key]);
                } else {
                    $request[$deny] = M_substr($request[$deny], 30);
                }

            }
            $msg = json_encode($request);
        }
        $data = [
            'admin_id'        => $adminId,
            'add_time'        => Carbon::now(),
            'module_name'     => MODULE_NAME,
            'controller_name' => CONTROLLER_NAME, //升级之后需要修改
            'action_name'     => ACTION_NAME,
            'model_name'      => $modelName,
            'request'         => $msg,
        ];
        return self::data($data)->add();
    }

    //删除全部日志
    public static function mDel_all()
    {
        return self::where('1 = 1')->delete();
    }
}
