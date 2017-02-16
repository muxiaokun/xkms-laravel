<?php

namespace App\Model;


class ArticleCategory extends Common
{
    protected $casts = [
        'extend'    => 'array',
        'attribute' => 'array',
    ];

    protected $orders = [
        'column'    => 'sort',
        'direction' => 'asc',
    ];

    public function getManageIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setManageIdAttribute($value)
    {
        $this->attributes['manage_id'] = $this->transfixionEncode($value);
    }

    public function getManageGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setManageGroupIdAttribute($value)
    {
        $this->attributes['manage_group_id'] = $this->transfixionEncode($value);
    }

    public function getAccessGroupIdAttribute($value)
    {
        return $this->transfixionDecode($value);
    }

    public function setAccessGroupIdAttribute($value)
    {
        $this->attributes['access_group_id'] = $this->transfixionEncode($value);
    }

    //返回子级所有分类id 数组集合
    //$pushMe 是否包含传入id
    public function scopeMFind_child_id($query, $id, $pushMe = true)
    {
        $where           = ['parent_id' => $id];
        $articleCategory = $query->select('id')->where($where)->get();
        $categoryChildId = [];
        foreach ($articleCategory as $category) {
            $categoryChildId[] = $category['id'];
            if (0 < $query->where(['parent_id' => $category['id']])->count()) {
                $articleCategoryChild = $query->mFind_child_id($category['id'], false);
                foreach ($articleCategoryChild as $child) {
                    $categoryChildId[] = $child;
                }
            }
        }
        if ($pushMe) {
            $categoryChildId[] = $id;
        }

        //不归组的任何人都可以管理;
        return $categoryChildId;
    }

    // 寻找分类的顶级分类
    public function scopeMFind_top($query, $id)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = $query->mFind_top_id($id);
        return $query->where('id', $articleCategoryTopId)->first();
    }

    // 寻找分类的顶级分类ID
    public function scopeMFind_top_id($query, $id)
    {
        if (!$id) {
            return false;
        }

        $categoryInfo = $query->select(['id', 'parent_id'])->where('id', $id)->first();
        if (0 != $categoryInfo['parent_id']) {
            return $query->mFind_top_id($categoryInfo['parent_id']);
        }

        return $categoryInfo['id'];
    }

    // 寻找分类的顶级分类列
    public function scopeMFindTopColumn($query, $id, $columnName)
    {
        if (!$id) {
            return false;
        }

        $articleCategoryTopId = $query->mFind_top_id($id);
        return $query->colWhere($articleCategoryTopId)->first()[$columnName];
    }

    //返回有权管理的频道
    public function scopeMFindAllow($query, $type = '')
    {
        switch ($type) {
            case 'ma':
                $query->transfixionWhere('manage_id', [session('backend_info.id')]);
                break;
            case 'mg':
                $query->transfixionWhere('manage_group_id', session('backend_info.group_id'));
                break;
            default:
                $query->transfixionWhere('manage_id', [session('backend_info.id')]);
                $query->transfixionWhere('manage_group_id', session('backend_info.group_id'));
        }
        $mFindAllow = $query->select('id')->get()->pluck('id');
        $mFindAllow->push(0);
        return $mFindAllow;
    }
}
