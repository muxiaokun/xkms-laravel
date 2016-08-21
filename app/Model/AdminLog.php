<?php

namespace App\Model;


class AdminLog extends Common
{
    //查询日志
    public function mSelect($where = null, $page = false)
    {
        $this->getPage($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        return $this->where($where)->select();
    }

    //添加日志 管理员编号 信息为空为传参 操作的模型
    public function mAdd($admin_id, $model_name = false, $msg = false)
    {
        if (!$admin_id) {
            return false;
        }

        if (!$model_name) {
            $model_name = "SYS_AUTO_LOG";
        }

        if (!$msg) {
            $deny_log_request = C('SYS_DENY_LOG_REQUEST');
            $request          = I('request.');
            foreach ($request as $key => $value) {
                if (in_array($key, $deny_log_request)) {
                    unset($request[$key]);
                } else {
                    $request[$deny] = M_substr($request[$deny], 30);
                }

            }
            $msg = json_encode($request);
        }
        $data = array(
            'admin_id'        => $admin_id,
            'add_time'        => time(),
            'module_name'     => MODULE_NAME,
            'controller_name' => CONTROLLER_NAME, //升级之后需要修改
            'action_name'     => ACTION_NAME,
            'model_name'      => $model_name,
            'request'         => $msg,
        );
        return $this->data($data)->add();
    }

    //删除全部日志
    public function mDel_all()
    {
        return $this->where('1 = 1')->delete();
    }
}
