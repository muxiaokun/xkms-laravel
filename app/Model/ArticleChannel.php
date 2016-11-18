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

        $articleChannel = $query->select('id')->where($where)->get();
        foreach ($articleChannel as $channel) {
            $mFindAllow[] = $channel['id'];
        }
        return $mFindAllow;
    }
}
