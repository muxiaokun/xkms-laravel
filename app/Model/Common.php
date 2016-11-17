<?php
// 公共

namespace App\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Common extends Model
{
    public $guarded = [];

    protected $whereClauses = [
        'Between',
        'NotBetween',
        'In',
        'NotIn',
        'Null',
        'NotNull',
        'Date',
        'Month',
        'Day',
        'Year',
    ];

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

    /**
     * 构造查询时用的like数组
     * @param array  $whereArr
     * @param string $logic AND or OR
     * @return boolean
     */
    public function scopeMMakeLikeArray($query, $where, $logic = 'OR')
    {
        //将$where转换成数组
        is_string($where) && $where = explode('|', $where);
        if (!$where) {
            return false;
        }

        foreach ($where as &$mid) {
            $mid = '%|' . $mid . '|%';
        }
        $result = [
            'like',
            $where,
        ];
        'OR' != $logic && array_push($result, $logic);
        return $result;
    }

    /**
     * 格式化编辑器生成的内容
     * 将内容的站内资源路径修改成相对路径
     * @param string $content
     * @return string
     */
    public function scopeMEncodeContent($query, $content)
    {
        //删除相对路径前的../
        $content = htmlspecialchars_decode($content);
        if (URL_REWRITE != config('system.url_model')) {
            return $content;
        }

        $urlpreg = MGetUrlpreg();
        return preg_replace($urlpreg['pattern'], $urlpreg['replacement'], $content);
    }

    /**
     * 格式化查询条件接口
     * @param type &$data
     */
    public function scopeMParseWhere($query, $wheres)
    {
        foreach ($wheres as $where) {
            if (3 == count($where)) {
                if (in_array($where[1], $this->whereClauses)) {
                    $whereClause = 'where' . $where[1];
                    $query->$whereClause($where[0], $where[2]);
                }
            } elseif (2 == count($where)) {
                $query->where($where[0], $where[1]);
            } else {
                throw new \Exception('parse where error!');
            }
        }
    }

    /**
     * 格式化数据接口
     * @param type &$data
     */
    public function scopeMEncodeData($query, $data)
    {
    }

    public function scopeMDecodeData($query, $data)
    {
    }
}
