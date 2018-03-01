<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/21
 * Time: 15:54
 */

namespace app\index\controller;

use app\index\lib\enum\ContractStatusEnum;
use app\index\model\Approval as ApprovalModel;
use app\index\model\ApprovalNode;
use app\index\model\Contract;
use app\index\controller\Contract as ContractController;
use app\index\model\User;
use think\Db;
use think\Exception;
use think\Request;
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
            $one = $approvalModel->where('contract_id', 'eq', $post['contract_id'])
                ->where('status', 'neq', 3)
                ->where('status', 'neq', 4)
                ->find();
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
                } else {
                    ContractController::setStatus($approvalModel->contract_id, ContractStatusEnum::WAITING);
                    // 记录合同变动日志
                    Log::add([
                        'model'=>'ContractLog',
                        'user_id'=>Session::get('admin.id'),
                        'contract_id'=>$approvalModel->contract_id,
                        'ip' => $this->request->ip(),
                        'location' => ip2address($this->request->ip()),
                        'note'=>"新增了审批 {$approvalModel->name}，关联合同状态自动更新为待审。关联合同为"]);
                }
                return $result;
            } catch (Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }
            return $result;
        }
        $users = User::all();
        $contracts = Contract::where('status', 0)->whereOr('status', 3)->field('id,name')->select();
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
                return 'hasStarted';
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
                } else {
                    ContractController::setStatus($approval->contract_id, ContractStatusEnum::WAITING);
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
            'title' => '全部审批管理',
            'approvals' => $approvals
        ]);
        return $this->fetch();
    }
    // cancel
    public function cancel()
    {
        $id = $this->request->param('id');
        if (!$this->request->isAjax() && !$this->request->isPost()) {
            return '非法操作';
        }
        // 验证审批是否存在
        $approval = ApprovalModel::get($id);
        if (!$approval) {
            return '审批不存在';
        }
        // 验证 是否在进行中
        if ($approval->status != 1) {
            return '只有进行中的审批才能撤销';
        }
        Db::startTrans();
        try {
            // 设置审批状态为 4
            $result = $approval->where('id', $id)->setField('status', 4);
            // 同步合同状为新录
            ContractController::setStatus($approval->contract_id, ContractStatusEnum::UNUSED);
            // 记录合同变动日志
            Log::add([
                'model' => 'ContractLog',
                'user_id' => Session::get('admin.id'),
                'contract_id' => $approval->contract_id,
                'ip' => Request::instance()->ip(),
                'location' => ip2address(Request::instance()->ip()),
                'note' => "撤销了审批 {$approval->name}，关联合同状态自动同步为新录。关联合同为"]);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return $result;
    }
    // del
    public function del()
    {
        if (!$this->request->isAjax() && !$this->request->isDelete()) {
            return -1;
        }

        $id = $this->request->param('id');

        // 验证审批状态，开启的审批不可删除
        // todo...
        $one = ApprovalModel::get($id);
        if ($one->start == 1) {
            return 'hasStarted';
        }
        if ($one->status <> 0){
            return '只有待审状态的审批可删除';
        }

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
            // 同步合同状态到新录
            ContractController::setStatus($one->contract_id, ContractStatusEnum::UNUSED);
            // 记录合同变动日志
            Log::add([
                'model'=>'ContractLog',
                'user_id'=>Session::get('admin.id'),
                'contract_id'=>$one->contract_id,
                'ip' => $this->request->ip(),
                'location' => ip2address($this->request->ip()),
                'note'=>"删除了审批 {$one->name}，关联合同状态自动更新为新录。关联合同为"]);
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
            $approvalModel = ApprovalModel::get($approvalId);
            $result = $approvalModel->save($data);

            // 设置合同状态为审批中
            ContractController::setStatus($approvalModel->contract_id, ContractStatusEnum::APPROVALING);
            // 记录合同变动日志
            Log::add([
                'model'=>'ContractLog',
                'user_id'=>Session::get('admin.id'),
                'contract_id'=>$approvalModel->contract_id,
                'ip' => $this->request->ip(),
                'location' => ip2address($this->request->ip()),
                'note'=>"开启了审批 {$approvalModel->name}，关联合同状态自动同步为审批中。关联合同为"]);

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
        Db::startTrans();
        try{
            $approval = ApprovalModel::get($approvalId);
            $data = ['start'=>2];
            // 同步合同
            ContractController::setStatus($approval->contract_id, ContractStatusEnum::WAITING);
            // 记录合同变动日志
            Log::add([
                'model'=>'ContractLog',
                'user_id'=>Session::get('admin.id'),
                'contract_id'=>$approval->contract_id,
                'ip' => Request::instance()->ip(),
                'location' => ip2address(Request::instance()->ip()),
                'note'=>"锁定了审批 {$approval->name}，关联合同状态自动同步为待审。关联合同为"]);
            $result = $approval->save($data);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return $result;
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