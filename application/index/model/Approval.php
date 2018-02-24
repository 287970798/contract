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

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function nodes()
    {
        return $this->hasMany('ApprovalNode', 'approval_id', 'id')->order('node_number asc');
    }

    public function currentNode()
    {
        return $this->belongsTo('ApprovalNode', 'current_node_id', 'id');
    }
}