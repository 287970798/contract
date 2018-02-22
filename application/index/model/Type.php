<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/20
 * Time: 12:15
 */

namespace app\index\model;


class Type extends BaseModel
{
    public function category()
    {
        return $this->belongsTo('Category', 'category_id', 'id');
    }
}