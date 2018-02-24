<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/21
 * Time: 15:54
 */

namespace app\index\controller;

use app\index\model\ApprovalNode;
use app\index\model\Contract;
use app\index\model\Approval as ApprovalModel;
use app\index\model\User;
use think\Db;
use think\Exception;
use think\Session;

class Approval extends BaseController
{
    protected $beforeActionList = [
        'checkAuth' => ['only' => 'all']
    ];
    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->post()) {
            $post = $this->request->post();
            $approvalModel = new ApprovalModel();
            // 验证关联的合同是否已被关联过
            $one = $approvalModel->where('contract_id', 'eq', $post['contract_id'])->find();
            if ($one) {
                return -1;
            }
            // 附加发起人ID
            $post['user_id'] = Session::get('admin.id');
            // 节点数据
            if (isset($post['nodes'])) {
                $nodes = $post['nodes'];
                $post['total_nodes'] = $nodes['total'];
                unset($post['nodes']);
                // 如果节点不为空则该审批可开启，否则不可开启
                if ($post['start'] == 1) {
                    if (empty($nodes['add'])) $post['start'] = 2;
                }
            }
            Db::startTrans();
            try{
                $result = $approvalModel->allowField(true)->save($post);
                // 给节点数据添加approve_id
                if (isset($nodes['add']) && !empty($nodes['add'])) {
                    foreach ($nodes['add'] as &$node) {
                        $node['approval_id'] = $approvalModel->id;
                    }
                    $nodeModel = new ApprovalNode();
                    $nodeModel->allowField(true)->saveAll($nodes['add']);
                }
                // 提交事务
                Db::commit();
                // 开启审批
                if ($post['start'] == 1) {
                    $this->start($approvalModel->id);
                }
                return $result;
            } catch (Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }
            return $result;
        }
        $users = User::all();
        $contracts = Contract::with('approval')->field('id,name')->select();
        $this->assign([
            'title' => '新建审批',
            'contracts' => $contracts,
            'users' => $users
        ]);
        return $this->fetch();
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isAjax() && $this->request->isPost()) {

            // 验证该审批流状态，如果已进入审批，则不能修改
            if (ApprovalModel::get($id)->start == 1) {
                return '当前审批为开启状态，不可修改';
            }

            $post = $this->request->post();
            // 节点数据
            if (isset($post['nodes'])) {
                $nodes = $post['nodes'];
                unset($post['nodes']);
                // 总节点数
                $post['total_nodes'] = $nodes['total'];

                // 如果节点不为空则该审批可开启，否则不可开启
                if ($post['start'] == 1) {
                    if ($nodes['total'] == 0) $post['start'] = 2;
                }
            }
            // 事务
            Db::startTrans();
            try{
                $approval = new ApprovalModel();
                $result = $approval->allowField(true)->save($post, ['id' => $id]);
                // 删除节点
                if (isset($nodes['del']) && !empty($nodes['del'])) {
                    foreach ($nodes['del'] as $item) {
                        ApprovalNode::destroy($item['id']);
                    }
                }
                // 新增节点
                if (isset($nodes['add']) && !empty($nodes['add'])) {
                    $approvalNodeModel = new ApprovalNode();
                    $approvalNodeModel->allowField(true)->saveAll($nodes['add']);
                }
                // 提交事务
                Db::commit();

                // 开启审批
                if ($post['start'] == 1) {
                    $this->start($id);
                }
                return $result;
            } catch (Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }
        }
        // 用户数据，用来组成被选择的审批人列表
        $users = User::all();
        $tmp = [];
        foreach ($users as $user) {
            $tmp[$user->id] = $user;
        }
        $users = json_encode($tmp);

        // 当前审批流数据
        $approval = ApprovalModel::with('user')->find($id);
        // 验证该审批流状态，如果已进入审批，则不能修改
        // todo...
//        if ($approval->start == 1) {
//            return '<script>alert("审批流已开启，不可修改");history.back()</script>';
//        }

        // 所有合同数据
        $contracts = Contract::with('approval')->field('id,name')->select();

        // 已存在的节点数据
        $nodesData = ApprovalNode::with('user')->where('approval_id', 'eq', $id)->order('node_number asc')->select();
        $nodes = [];
        $totalNodes = $maxNodeNum = 0;
        if ($nodesData) {
            foreach ($nodesData as $node) {
                $tmp[$node->user->id] = $node;
                $nodes[$node->user->id] = [
                    'id' => $node->id,
                    'user_id' => $node->user->id,
                    'node_number' => $node->node_number
                ];
                // 记录最大节点排序号
                $maxNodeNum = $node->node_number;
            }
        }
        // 总节点数
        $totalNodes = count($nodes);
        // 现有节点数据转换成json，以备前端js处理
        $nodes = json_encode($nodes);

        $this->assign([
            'title' => '审批修改',
            'approval' => $approval,
            'contracts' => $contracts,
            'users' => $users,
            'nodes' => $nodes,
            'nodesData' => $nodesData,
            'totalNodes' => $totalNodes,
            'maxNodeNum' => $maxNodeNum
        ]);
        return $this->fetch();
    }
    //list
    public function all()
    {
        $approvals = ApprovalModel::with('contract')->order('id desc')->select();
        $this->assign([
            'title' => '审批管理',
            'approvals' => $approvals
        ]);
        return $this->fetch();
    }
    // del
    public function del()
    {
        if (!$this->request->isAjax() && !$this->request->isDelete()) {
            return -1;
        }
        // 验证审批状态，开启的审批不可删除
        // todo...

        $id = $this->request->param('id');
        Db::startTrans();
        try{
            $result = ApprovalModel::destroy($id);
            // 取出节点
            $nodes = ApprovalNode::where('approval_id', 'eq', $id)->field('id')->select();
            // 删除节点
            if ($nodes) {
                foreach ($nodes as $node) {
                    ApprovalNode::destroy($node->id);
                }
            }
            // 提交事务
            Db::commit();
            return $result;
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    // get approval by id
    public function approvalById()
    {
        $id = $this->request->param('id');
        $approval = ApprovalModel::with(['contract'=>['type','customer','linkman'],'user','nodes'])->find($id);
        $this->assign([
            'title' => '查看审批流',
            'approval' => $approval
        ]);
        return $this->fetch('one');
    }

    // get approvals by user id
    public function ApprovalByUser()
    {
        $userId = $this->request->param('id');
        $approvals = ApprovalModel::with('contract')
            ->where('user_id', 'eq', $userId)
            ->order('id desc')
            ->select();
        $this->assign([
            'title' => '我发起的审批',
            'approvals' => $approvals
        ]);
        return $this->fetch('all');
    }
    // update status
    public function setStatus()
    {

    }
    // update start

    /**
     * start=1
     * first node start
     * 如果审批还没开始，则同时开启第一个节点
     * 如果审批过程中的流程处于锁定状态，则只打开锁定状态即可。
     */
    public function start($approvalId)
    {
        // 这里要时行权限验证
        // 只有管理员和发起用户才能启用和关闭审批流
        // todo...

        Db::startTrans();
        try {
            // 开启第一个节点
            $firstNode = ApprovalNode::where('approval_id', 'eq', $approvalId)->order('node_number asc')->find();
            if (!$firstNode) {
                throw new Exception('当前审批没有节点');
            }
            if ($firstNode->status == -1) {     // 如果是第1次开启
                // 开启第1个节点
                $firstNode->status = 0;
                $firstNode->save();

                $data = [
                    'start' => 1,   // 审批开启
                    'status' => 1,  // 审批中
                    'current_node_id' => $firstNode->id     // 当前审批节点
                ];
            } else {    // 如果是中途暂停的
                $data = [
                    'start' => 1
                ];
            }

            // 开启审批并将第1个节点设置为当前节点,审批状态设置为审批中
            $approvalModel = new ApprovalModel();
            $result = $approvalModel->save($data, ['id' => $approvalId]);

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return $result;
    }

    /**
     * @return false|int|string
     */
    public function setStart()
    {
        if (!$this->request->isAjax() || !$this->request->isPost()) {
            return '非法操作';
        }
        $id = $this->request->param('id');
        $start = $this->request->post('start');
        if ($start == 1) {
            $result = $this->start($id);
        } else {
            $result = $this->stop($id);
        }
        return $result;
    }

    /**
     * stop
     * 随时可停止
     *
     */
    public static function stop($approvalId)
    {
        $approval = ApprovalModel::get($approvalId);
        $data = ['start'=>2];
        return $approval->save($data);
    }
    // update total nodes
    public function setTotalNodes()
    {

    }
    // update current node
    public function setCurrentNode()
    {

    }
    // update process
    public function setProcess()
    {

    }
}