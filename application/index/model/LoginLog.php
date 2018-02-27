<?php


namespace app\index\model;


class LoginLog extends BaseModel
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }
}