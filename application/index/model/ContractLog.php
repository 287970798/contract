<?php


namespace app\index\model;


class ContractLog extends BaseModel
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function contract()
    {
        return $this->belongsTo('Contract', 'contract_id', 'id');
    }
}