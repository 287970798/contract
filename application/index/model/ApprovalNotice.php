<?php


namespace app\index\model;


class ApprovalNotice extends BaseModel
{
    public function approval()
    {
        return $this->belongsTo('Approval', 'approval_id', 'id');
    }
}