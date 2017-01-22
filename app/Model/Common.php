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
     * @param $value
     * @return \Illuminate\Support\Collection
     * 解析 |*| 模式
     */
    public function parseGetIdAttribute($value)
    {
        $collect = collect(explode('|', $value));
        $collect->shift();
        $collect->pop();
        return $collect;
    }

    /**
     * @param $value
     * @return string
     * 组合 |*| 模式
     */
    public function parseSetIdAttribute($value)
    {
        $collect      = collect($value);
        $newAttribute = '|' . $collect->implode('|') . '|';
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

    public function scopeIdWhere($query, $id, $column = 'id')
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
