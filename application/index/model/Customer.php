<?php


namespace app\index\model;


class Customer extends BaseModel
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }
}