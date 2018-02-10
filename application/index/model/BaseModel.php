<?php


namespace app\index\model;


use think\Model;
use traits\model\SoftDelete;

class BaseModel extends Model
{
    // 软删除
    use SoftDelete;
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    // 数据集对象的转换
    // protected $resultSetType = 'collection';
}