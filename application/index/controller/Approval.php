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

class Approval extends BaseController
{
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
            // 节点数据
            if (isset($post['nodes'])) {
                $nodes = $post['nodes'];
                unset($post['nodes']);
            }
            Db::startTrans();
            try{
                $result = $approvalModel->allowField(true)->save($post);
                // 给节点数据添加approve_id
                if (isset($nodes) && !empty($nodes)) {
                    foreach ($nodes as &$node) {
                        $node['approval_id'] = $approvalModel->id;
                    }
                    $nodeModel = new ApprovalNode();
                    $nodeModel->allowField(true)->saveAll($nodes);
                }
                // 提交事务
                Db::commit();
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
            $post = $this->request->post();
            $approval = new ApprovalModel();
            $reuslt = $approval->allowField(true)->save($post, ['id' => $id]);
            return $reuslt;
        }
        $users = User::all();
        $tmp = [];
        foreach ($users as $user) {
            $tmp[$user->id] = $user;
        }
        $users = json_encode($tmp);

        $approval = ApprovalModel::get($id);
        $contracts = Contract::with('approval')->field('id,name')->select();

        $nodes = ApprovalNode::with('user')->where('approval_id', 'eq', $id)->order('node_number asc')->select();
        $tmp = [];
        foreach ($nodes as $node) {
            $tmp[$node->user->id] = $node;
        }
        $nodes = json_encode($tmp);

        $this->assign([
            'title' => '审批修改',
            'approval' => $approval,
            'contracts' => $contracts,
            'users' => $users,
            'nodes' => $nodes,
            'nodes2' => $tmp
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

    // update status
    public function setStatus()
    {

    }
    // update start
    public function setStart()
    {

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