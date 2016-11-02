<?php

namespace App\Model;

class AdminLogs extends Common
{
    //查询日志
    public function scopeMList($query, $where = null, $page = false)
    {
        $query->mParseWhere($where);
        //null !== $query->options['order'] && $query->order('add_time desc');
        return $query->mGetPage($page);
    }

    //添加日志 管理员编号 信息为空为传参 操作的模型
    public function scopeMAdd($query, $adminId, $message = false, $msg = false)
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
                    $request[$key] = mSubstr($request[$key], 30);
                }

            }
            $request_json = json_encode($request);
        }
        $data = [
            'admin_id'   => $adminId,
            'route_name' => request()->route()->getName(),
            'message'    => $message,
            'request'    => $request_json,
        ];
        return $query->mAdd($data);
    }

    //删除全部日志
    public function scopeMDel_all($query)
    {
        return $query->where('1 = 1')->delete();
    }
}
