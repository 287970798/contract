<?php


namespace app\index\model;


class Linkman extends BaseModel
{
    public function customer()
    {
        return $this->belongsTo('Customer', 'customer_id', 'id');
    }
}