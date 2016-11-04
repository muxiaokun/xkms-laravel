<?php

namespace App\Model;


class ArticleChannel extends Common
{
    //返回有权管理的频道
    public function scopeMFind_allow($query, $type = true)
    {
        $where = [];
        //ma = manage admin 编辑属主 属组
        if (true === $type || 'ma' == $type) {
            $where['manage_id'] = session('backend_info.id');
        }

        //mg = manage group 编辑 基本信息
        if (true === $type || 'mg' == $type) {
            $where['manage_group_id'] = session('backend_info.group_id');
        }

        $mFindAllow = [0];
        if (empty($where['manage_id']) && empty($where['manage_group_id'])) {
            return $mFindAllow;
        }

        $articleChannel = $query->select('id')->mSelect($where);
        foreach ($articleChannel as $channel) {
            $mFindAllow[] = $channel['id'];
        }
        return $mFindAllow;
    }

    public function scopeMParseWhere($query, $where)
    {
        if (is_null($where)) {
            return;
        }

        isset($where['manage_id']) && $where['manage_id'] = $query->mMakeLikeArray($where['manage_id']);
        isset($where['manage_group_id']) && $where['manage_group_id'] = $query->mMakeLikeArray($where['manage_group_id']);

        if ($where['manage_id'] && $where['manage_group_id']) {
            $where['_complex'] = [
                '_logic'          => 'or',
                'manage_id'       => $where['manage_id'],
                'manage_group_id' => $where['manage_group_id'],
            ];
            unset($where['manage_id']);
            unset($where['manage_group_id']);
        }
    }

    public function scopeMEncodeData($query, $data)
    {
        isset($data['manage_id']) && $data['manage_id'] = '|' . implode('|', $data['manage_id']) . '|';
        isset($data['manage_group_id']) && $data['manage_group_id'] = '|' . implode('|',
                $data['manage_group_id']) . '|';
        isset($data['access_group_id']) && $data['access_group_id'] = serialize($data['access_group_id']);
        isset($data['ext_info']) && $data['ext_info'] = serialize($data['ext_info']);
    }

    public function scopeMDecodeData($query, $data)
    {
        isset($data['manage_id']) && $data['manage_id'] = explode('|',
            substr($data['manage_id'], 1, strlen($data['manage_id']) - 2));
        isset($data['manage_group_id']) && $data['manage_group_id'] = explode('|',
            substr($data['manage_group_id'], 1, strlen($data['manage_group_id']) - 2));
        isset($data['access_group_id']) && $data['access_group_id'] = unserialize($data['access_group_id']);
        isset($data['ext_info']) && $data['ext_info'] = unserialize($data['ext_info']);
    }
}
