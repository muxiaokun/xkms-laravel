<?php
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    protected $guarded = [];

    protected $orders = [
        'id' => 'desc',
    ];

    public function scopeMOrdered($query, $ordered = [])
    {
        if ($ordered && is_array($ordered)) {
            $this->orders = $ordered;
        }
        foreach ($this->orders as $orderBy => $orderDirection) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query;
    }

    /**
     * @param $query
     * @return array
     * 获取表列空数据
     */
    public function scopeColumnEmptyData($query)
    {
        $columns       = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $empty_columns = [];
        foreach ($columns as $column) {
            $empty_columns[$column] = '';
        }

        return $empty_columns;
    }

    /**
     * @param $query
     * @param $id
     * @param string $column
     * 列条件 默认id 支持IN数组
     */
    public function scopeColWhere($query, $id, $column = 'id')
    {
        if (is_array($id)) {
            $query->whereIn($column, $id);
        } else {
            $query->where($column, $id);
        }

        return $query;
    }

    public function scopeTimeWhere($query, $column, $timeRange)
    {
        $startInputName = $column . '_start';
        $endInputName   = $column . '_end';
        if (isset($timeRange[$startInputName])) {
            $query->where($column, '>=', $timeRange[$startInputName]);
        }
        if (isset($timeRange[$endInputName])) {
            $query->where($column, '<=', $timeRange[$endInputName]);
        }

        return $query;
    }

    /**
     * @param $query
     * @param $column
     * @param $ids
     * 建立like %||% 查询
     */
    public function scopeTransfixionWhere($query, $column, $ids, $or = true)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $query->where(function ($query) use ($column, $ids, $or) {
            foreach ($ids as $id) {
                if ($or) {
                    $query->orWhere($column, 'like', '%|' . $id . '|%');
                } else {
                    $query->where($column, 'like', '%|' . $id . '|%');
                }

            }
        });

        return $query;
    }

    /**
     * @param $string 字符串
     * @param $useKey 是否解析Key
     * @return \Illuminate\Support\Collection
     *                解析 |*| 模式
     */
    public function transfixionDecode($string, $useKey = false)
    {
        $collect = collect(explode('|', $string));
        $collect->shift();
        $collect->pop();

        if ($useKey) {
            $useKeyCollect = collect();
            $collect->each(function ($item, $key) use ($useKeyCollect) {
                if ($item) {
                    list($trueKey, $trueItem) = explode(':', $item);
                    $useKeyCollect->put($trueKey, $trueItem);
                }
            });
            return $useKeyCollect;
        }

        return $collect;
    }

    /**
     * @param $value  数组
     * @param $useKey 是否组合Key
     * @return string
     *                组合 |*| 模式
     */
    public function transfixionEncode($value, $useKey = false)
    {
        $collect = collect($value)->filter()->sort();

        if ($useKey) {
            $useKeyCollect = collect();
            $collect->each(function ($item, $key) use ($useKeyCollect) {
                $useKeyCollect->push($key . ':' . $item);
            });
            $newAttribute = '|' . $useKeyCollect->implode('|') . '|';
        } else {
            $newAttribute = '|' . $collect->implode('|') . '|';
        }

        return $newAttribute;
    }
}
