<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/21
 * Time: 15:53
 */

namespace app\index\model;


class Approval extends BaseModel
{
    public function contract()
    {
        return $this->belongsTo('Contract', 'contract_id', 'id');
    }
}