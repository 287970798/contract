<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/21
 * Time: 15:54
 */

namespace app\index\model;


class ApprovalNode extends BaseModel
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }
}