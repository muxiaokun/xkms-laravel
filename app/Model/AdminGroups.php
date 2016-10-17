<?php

namespace App\Model;


class AdminGroups extends Common
{
    //获得全部或者部分管理组列表
    public static function mSelect($where = null, $page = false)
    {
        self::mParseWhere($where);
        self::mGetPage($page);
        null !== self::options['order'] && self::order('id desc');
        $data = self::where($where)->select();
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        return $data;
    }

    public static function mDel($id)
    {
        if (!$id || 1 == $id || (is_array($id) && in_array(1, $id))) {
            return false;
        }
        return parent::mDel($id);
    }

    //查找出组权限
    public static function mFind_privilege($id)
    {
        if (!$id) {
            return false;
        }

        is_array($id) && $id = ['in', $id];
        $data = self::where(['id' => $id, 'is_enable' => 1])->select(['privilege']);
        foreach ($data as &$dataRow) {
            self::mDecodeData($dataRow);
        }
        $privilege = [];
        foreach ($data as $group) {
            $privilege = array_merge($privilege, $group['privilege']);
        }
        return $privilege;
    }

    //返回有权管理的组
    public static function mFind_allow()
    {
        $where       = [
            'manage_id' => session('backend_info.id'),
        ];
        $manageGroup = self::field('id')->mSelect($where);
        $mFindAllow  = [];
        foreach ($manageGroup as $group) {
            $mFindAllow[] = $group['id'];
        }
        //不归组的任何人都可以管理
        $mFindAllow[] = 0;
        return $mFindAllow;
    }

    protected static function mParseWhere(&$where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id'] = self::mMakeLikeArray($where['manage_id']);
    }

    //检查和格式化数据
    protected static function mEncodeData(&$data)
    {
        if (isset($data['id']) && (1 == $data['id'] || (is_array($data['id']) && in_array(1, $data['id'])))) {
            unset($data['privilege']);
        }
        isset($data['manage_id']) && $data['manage_id'] = '|' . implode('|', array_unique($data['manage_id'])) . '|';
        isset($data['privilege']) && $data['privilege'] = implode('|', $data['privilege']);
        isset($data['ext_template']) && $data['ext_template'] = serialize($data['ext_template']);
    }

    //检查和去除格式化数据
    protected static function mDecodeData(&$data)
    {
        isset($data['manage_id']) && $data['manage_id'] = explode('|',
            substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['privilege']) && $data['privilege'] = explode('|', $data['privilege']);
        isset($data['ext_template']) && $data['ext_template'] = unserialize($data['ext_template']);
    }
}