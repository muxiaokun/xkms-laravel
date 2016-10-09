<?php

namespace App\Model;


class AdminLog extends Common
{
    //查询日志
    public function mSelect($where = null, $page = false)
    {
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('add_time desc');
        return $this->where($where)->select();
    }

    //添加日志 管理员编号 信息为空为传参 操作的模型
    public function mAdd($adminId, $modelName = false, $msg = false)
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
            'add_time'        => time(),
            'module_name'     => MODULE_NAME,
            'controller_name' => CONTROLLER_NAME, //升级之后需要修改
            'action_name'     => ACTION_NAME,
            'model_name'      => $modelName,
            'request'         => $msg,
        ];
        return $this->data($data)->add();
    }

    //删除全部日志
    public function mDel_all()
    {
        return $this->where('1 = 1')->delete();
    }
}
