<?php

namespace App\Model;

class AdminLog extends Common
{
    protected $casts = [
        'request' => 'array',
    ];

    //添加日志 管理员编号 信息为空为传参 操作的模型
    public static function record($adminId, $message = false, $msg = false)
    {
        if (!$adminId) {
            return false;
        }

        if (!$message) {
            $message = "SYS_AUTO_LOG";
        }

        if (!$msg) {
            $denyLogRequest = config('system.sys_deny_log_request');
            $request        = request()->all();
            foreach ($request as $key => $value) {
                if (in_array($key, $denyLogRequest)) {
                    unset($request[$key]);
                } else {
                    if (is_array($request[$key])) {
                        $request[$key] = json_encode($request[$key]);
                    }
                    $request[$key] = mSubstr($request[$key], 30);
                }

            }
        }
        $data = [
            'admin_id'   => $adminId,
            'route_name' => request()->route()->getName(),
            'message'    => $message,
            'request'    => $request,
        ];
        return (new static)->create($data);
    }
}
