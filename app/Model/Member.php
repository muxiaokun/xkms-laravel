<?php

namespace App\Model;


class Member extends Common
{
    public function mSelect($where = null, $page = false)
    {
        $this->mParseWhere($where);
        $this->mGetPage($page);
        !isset($this->options['order']) && $this->order('id desc');
        $data = $this->field('*,inet_ntoa(login_ip) as aip')->where($where)->select();
        foreach ($data as &$dataRow) {
            $this->mDecodeData($dataRow);
        }
        return $data;
    }

    public function mAdd($data)
    {
        if (!$data) {
            return false;
        }

        $data['register_time'] = time();
        return parent::mAdd($data);
    }

    public function mFind($id)
    {
        $this->field('*,inet_ntoa(login_ip) as aip');
        return parent::mFind($id);
    }

    public function authorized($user, $pwd, $memberId)
    {
        if (!$user && !$memberId) {
            return false;
        }

        if ($memberId) {
            $where = [
                'id' => $memberId,
            ];
        } else {
            $where = [
                'member_name' => $user,
                'is_enable'   => '1',
            ];
        }
        $memberInfo = $this->where($where)->find();
        if ($memberInfo['member_pwd'] == md5($pwd . $memberInfo['member_rand']) || $memberId) {
            $data = [
                'last_time' => time(),
                'login_ip'  => ['exp', 'inet_aton("' . $_SERVER['REMOTE_ADDR'] . '")'],
            ];
            $this->where(['id' => $memberInfo['id']])->data($data)->save();
            $memberInfo = $this->mFind($memberInfo['id']);
            return $memberInfo;
        } else {
            return false;
        }
    }

    protected function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['group_id']) && $where['group_id'] = $this->mMakeLikeArray($where['group_id']);
    }

    protected function mEncodeData(&$data)
    {
        if ($data['member_pwd']) {
            $randStr             = $this->_make_rand();
            $data['member_pwd']  = md5($data['member_pwd'] . $randStr);
            $data['member_rand'] = $randStr;
        } else {
            unset($data['member_pwd']);
            unset($data['member_rand']);
        }
        !isset($data['group_id']) && $data['group_id'] = [1];
        //组合权限
        isset($data['group_id']) && $data['group_id'] = '|' . implode('|', $data['group_id']) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    protected function mDecodeData(&$data)
    {
        unset($data['member_pwd']);
        unset($data['member_rand']);
        isset($data['group_id']) && $data['group_id'] = explode('|',
            substr($data['group_id'], 1, strlen($data['group_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
