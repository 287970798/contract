<?php


namespace app\index\controller;


use app\index\model\ApprovalNode;
use think\Db;
use think\Exception;
use app\index\model\Approval;

class Node extends BaseController
{
    public function approval()
    {
        /**
         * 获取当前用户的所有审批节点包含对应审批流
         *
         * 启用审批
         * approval.start = 1
         * start the first node
         *
         * 开启节点(start node)：
         * node.status = 0
         * approval.current_node_id = node.id
         * approval.status = 1
         *
         * 审核流程：
         * 当前节点status=1或status=2 及审批意见note
         *
         * 如果status=1，则开启下个节点status=0
         * approval current_node_id=下个节点的id, process+1 ，status=1(审批中)
         * 如果没有下个节点，则process+1,status=2(审批通过)
         * if (node.status == 1) {
         *    node.status=1
         *    node.note=note
         *    if(nextNode) {
         *      nextNode.status=0
         *      approval.current_node_id = nextNode.id
         *      approval.status=1; 审批中
         *    } else {
         *      approval.status=2; 审批通过
         *    }
         * } elseif (node.status == 2) {
         * approval.status=3; 审批不通过
         * }
         * approval.process++; 审批数加1
         *
         */
        $userId = $this->request->param('id');
        $nodes = ApprovalNode::with(['approval' => ['contract', 'user', 'nodes' => ['user']]])
            ->where('user_id', 'eq', $userId)
            ->where('status', 'neq', -1)
            ->select();
        $this->assign([
            'title' => '我的审批',
            'nodes' => $nodes
        ]);
        return $this->fetch();
    }

    public function check()
    {
        // 通过
        // 设置当前节点status+note
        // 如果有下个节点 开启下个节点status=0 审批流status=1 current_node_id=nextNode.id
        // 如果没有下个节点 审批流status=2 审批流结束
        if (!$this->request->isAjax() || !$this->request->isPost()) {
            return '非法操作';
        }

        $post = $this->request->post();
        $approvalId = $post['approval_id'];
        unset($post['approval_id']);

        Db::startTrans();
        try {
            // 更新当前节点审核数据
            $nodeModel = ApprovalNode::get($post['id']);
            if ($nodeModel->status != 0) {
                throw new Exception('您已审批过，不可重复审批');
            }
            $result = $nodeModel->isUpdate(true)->allowField(true)->save($post);
            // 进度
            Approval::where('id', $approvalId)->setInc('process');
//            Db::table('approval')->where('id', $approvalId)->setInc('process');

            if ($post['status'] == 1) {
                // 获取该审批所有节点
                $nodes = $nodeModel->where('approval_id', 'eq', $approvalId)
                    ->order('node_number asc')->select()->toArray();
                // 下一个节点
                $nextNode = $this->nextNode($nodes, $nodeModel->id);
                if ($nextNode) {
                    // 启用下个节点
                    ApprovalNode::update(['id' => $nextNode['id'], 'status' => 0]);
                    // 设置当前节点
                    Approval::update([
                        'id' => $approvalId,
                        'status' => 1,
                        'current_node_id' => $nextNode['id']
                    ]);
                } else {
                    Approval::update([
                        'id' => $approvalId,
                        'status' => 2   // 审批流通过结束
                    ]);
                }
            } elseif ($post['status'] == 2) {
                Approval::update([
                    'id' => $approvalId,
                    'status' => 3   // 审批流驳回结束
                ]);
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return $result;
    }

    /**
     * 下一个节点
     * @param $nodes
     * @param $currentId
     * @return bool
     */
    public function nextNode($nodes, $currentId)
    {
        $lastNode = end($nodes);
        if ($lastNode['id'] == $currentId) {
            return false;
        } else {
            foreach ($nodes as $key => $node) {
                if ($node['id'] == $currentId) {
                    $nextNode = $nodes[$key + 1];
                    break;
                }
            }
        }
        return $nextNode;
    }

    /**
     * 开启节点(start node)：
     * node.status = 0
     * approval.current_node_id = node.id
     * approval.status = 1
     *
     * @param $nodeId
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function startNode($nodeId)
    {
        $nodeModel = new ApprovalNode();
        $node = $nodeModel->find($nodeId);
        if ($node->status != -1) {
            return '当前节点是开启状态，无法重复开启';
        }
        $approvalId = $node->approval_id;
        $approvalModel = new Approval();
        $approval = $approvalModel->find($approvalId);
        Db::startTrans();
        try {
            // 节点状态
            $node->status = 0;
            $result = $node->save();
            // 审批状态 当前审批节点
            $approval->current_node_id = $nodeId;
            $approval->status = 1;
            $approval->save();
            Db::commit();
            return $result;
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        $node = ApprovalNode::get($nodeId);
    }
}