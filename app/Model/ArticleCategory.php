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

    public function setThumbAttribute($value)
    {
        $this->attributes['thumb'] = mParseUploadUrl($value);
    }

    public function getContentAttribute($value)
    {
        return mParseContent($value, true);
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = mParseContent($value);
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
                $query->orWhere(function ($query) {
                    $query->transfixionWhere('manage_id', [session('backend_info.id')]);
                });
                $query->orWhere(function ($query) {
                    $query->transfixionWhere('manage_group_id', session('backend_info.group_id'));
                });
        }
        $mFindAllow = $query->select('id')->get()->pluck('id');
        $mFindAllow->push(0);
        return $mFindAllow;
    }

    public function scopeMFindTopId($query, $id)
    {
        $categoryInfo = $query->colWhere($id)->select(['parent_id'])->first();
        if (null !== $categoryInfo && $categoryInfo['parent_id']) {
            return ArticleCategory::scopeMFindTopId($query, $categoryInfo['parent_id']);
        }
        return collect($id);
    }

    public function scopeMFindCateChildIds($query, $id)
    {
        $childIds = collect();
        ArticleCategory::colWhere($id, 'parent_id')->get()->each(function ($item, $key) use ($query, &$childIds) {
            ArticleCategory::scopeMFindCateChildIds($query, $item->id)->each(function ($item, $key) use ($childIds) {
                $childIds->push($item);
            });
        });
        $childIds->push((int)$id);
        return $childIds;
    }

    public function scopeMCategoryTree($query, $id)
    {
        $categoryTree = collect();
        $categoryInfo = ArticleCategory::colWhere($id)->select(['parent_id'])->first();
        if (null !== $categoryInfo || 0 === $id) {
            $parentId     = 0 === $id ? $id : $categoryInfo['parent_id'];
            $categoryList = ArticleCategory::where('parent_id', $parentId)->select([
                'id',
                'name',
            ])->get();
            if (0 < $parentId) {
                $newCategoryTree = ArticleCategory::scopeMCategoryTree($query, $parentId);
                $newCategoryTree->each(function ($item, $key) use ($categoryTree) {
                    $categoryTree->push($item);
                });
            }
            $categoryTree->push([
                'id' => $id,
                'category_list' => $categoryList,
            ]);
        }
        return $categoryTree;
    }
}
