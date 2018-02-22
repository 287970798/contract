<?php


namespace app\index\controller;


use app\index\model\ApprovalNode;

class Node extends BaseController
{
    public function approval()
    {
        /**
         * 获取当前用户的所有审批节点包含对应审批流
         *
         */
        $userId = $this->request->param('id');
        $nodes = ApprovalNode::with(['approval' => ['contract', 'user', 'nodes'=>['user']]])
            ->where('user_id', 'eq', $userId)
            ->where('status', 'neq', -1)
            ->select();
        $this->assign([
            'title' => '我的审批',
            'nodes' => $nodes
        ]);
        return $this->fetch();
    }
}