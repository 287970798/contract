<?php
namespace app\index\controller;


use app\index\model\ApprovalNode;
use think\Session;
use app\index\model\Contract;
use app\index\model\Approval;
use app\index\model\Customer;
use app\index\model\Linkman;

class Index extends BaseController
{
    public function index()
    {
        $admin = Session::get('admin');
        $contracts = $this->contract($admin);
        $approvals = $this->approval($admin);
        $customers = $this->customer($admin);
        $linkmans = $this->linkman($admin);
        $this->assign([
            'title' => '控制台',
            'contracts' => $contracts,
            'approvals' => $approvals,
            'customers' => $customers,
            'linkmans' => $linkmans,
            'icon' => '<span class="layui-icon">&#xe628;</span>'
        ]);
        return $this->fetch();
    }

    public function contract($user)
    {
        $new = Contract::where('manager', $user['name'])->order('id desc')->limit(5)->select();
        $expire = Contract::where('manager', $user['name'])
            ->where('due_date', '<', strtotime("+1 month"))
            ->order('due_date asc, id desc')
            ->limit(5)
            ->select();

        $expiring = $overdue = [];
        foreach ($expire as $item) {
            $days = ceil(($item['due_date'] - time()) / (3600 * 24));
            $item['days'] = $days;
            if ($days >= 0) {
                $expiring[] = $item;
            } else {
                $overdue = $item;
            }
        }
        return [
            'new' => $new,
            'expiring' => $expiring,
            'overdue' => $overdue
        ];
    }

    public function approval($user)
    {
        // 用户发起
        $beSponsor = Approval::with(['contract','currentNode'=>['user']])
            ->where('user_id', $user['id'])
            ->where('start', 1)
            ->limit(5)->select();
        // 用户审批
        $beApporval = Approval::with('contract')
            ->where('current_node_id', $user['id'])
            ->limit(5)
            ->order('update_time desc')
            ->select();
        return [
            'beSponsor' => $beSponsor,
            'beApproval' => $beApporval
        ];
    }

    public function customer($user)
    {
        $customers = Customer::where('user_id', $user->id)->limit(5)->order('id desc')->select();
        return $customers;
    }

    public function linkman($user)
    {
        $linkmans = Linkman::where('user_id', $user->id)->limit(5)->order('id desc')->select();
        return $linkmans;
    }


}
