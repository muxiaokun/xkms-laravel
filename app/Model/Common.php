<?php
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    public $guarded = [];

    public $orders = [
        'id' => 'desc',
    ];


    /**
     * @param $string 字符串
     * @param $useKey 是否解析Key
     * @return \Illuminate\Support\Collection
     * 解析 |*| 模式
     */
    public function transfixionDecode($string, $useKey = false)
    {
        $collect = collect(explode('|', $string));
        $collect->shift();
        $collect->pop();

        if ($useKey) {
            $useKeyCollect = collect();
            $collect->each(function ($item, $key) use ($useKeyCollect) {
                list($trueKey, $trueItem) = explode(':', $item);
                $useKeyCollect->put($trueKey, $trueItem);
            });
            return $useKeyCollect;
        }

        return $collect;
    }

    /**
     * @param $value 数组
     * @param $useKey 是否组合Key
     * @return string
     * 组合 |*| 模式
     */
    public function transfixionEncode($value, $useKey = false)
    {
        $collect = collect($value);

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

    public function scopeMOrdered($query)
    {
        foreach ($this->orders as $orderBy => $orderDirection) {
            $query->orderBy($orderBy, $orderDirection);
        }

    }

    public function scopeMGetColumn($query)
    {
        $columns       = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $empty_columns = [];
        foreach ($columns as $column) {
            $empty_columns[$column] = '';
        }
        return $empty_columns;
    }

    public function scopeColWhere($query, $id, $column = 'id')
    {
        if (is_array($id)) {
            $query->whereIn($column, $id);
        } else {
            $query->where($column, $id);
        }

    }

    public function scopeLikeWhere($query, $column, $value)
    {
        if (!$column || !$value) {
            return $this;
        }
        !is_array($value) && $value = explode('|', $value);
        $query->where(function ($query) use ($column, $value) {
            foreach ($value as &$mid) {
                $query->orWhere($column, 'like', '%|' . $mid . '|%');
            }
        });
    }
}
