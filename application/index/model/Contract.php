<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/20
 * Time: 18:18
 */

namespace app\index\model;


class Contract extends BaseModel
{
    public function category()
    {
        return $this->belongsTo('Category', 'category_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo('Type', 'type_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('Customer', 'customer_id', 'id');
    }

    public function linkman()
    {
        return $this->belongsTo('linkman', 'linkman_id', 'id');
    }

    public function file()
    {
        return $this->hasOne('ContractFile', 'contract_id', 'id');
    }

    public function extra()
    {
        return $this->hasMany('ContractExtra', 'contract_id', 'id');
    }

    public function approval()
    {
        return $this->hasOne('Approval', 'contract_id', 'id');
    }

}