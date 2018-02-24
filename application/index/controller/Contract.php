<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/20
 * Time: 18:18
 */

namespace app\index\controller;

use app\index\model\Category;
use app\index\model\ContractExtra;
use app\index\model\ContractFile;
use app\index\model\Type;
use app\index\model\Customer;
use app\index\model\Linkman;
use think\Db;
use think\Exception;
use think\Session;
use app\index\model\Contract as ContractModel;
use app\index\model\Approval;

class Contract extends BaseController
{
    protected $beforeActionList = [
        'checkAuth' => ['only' => '']
    ];

    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $contract = new ContractModel();
            // 检测合同编号是否已存在
            $one = $contract->where('contract_sn', 'eq', $post['contract_sn'])->find();
            if ($one) {
                return -1; // 合同编号存在
            }
            $post['short_sn'] = explode('-', $post['contract_sn'])[2];
            $post['contract_date'] = strtotime($post['contract_date']);
            $post['due_date'] = strtotime($post['due_date']);
            // 获取附加项数据
            if (isset($post['extra'])) {
                $extra = $post['extra'];
                unset($post['extra']);
            }
            // 获取上传数据
            if (isset($post['contract_file'])) {
                $file = $post['contract_file'];
                unset($post['contract_file']);
            }
            Db::startTrans();
            try{
                $result = $contract->allowField(true)->save($post);
                // 附加数据添加contract_id
                if (isset($extra) && !empty($extra)) {
                    foreach ($extra as &$item) {
                        $item['contract_id'] = $contract->id;
                    }
                    $extraModel = new ContractExtra;
                    $extraModel->saveAll($extra);
                }
                // 附件数据添加contract_id
                if (isset($file) && !empty($file['src'])) {
                    $file['contract_id'] = $contract->id;
                    $contractFileModel = new ContractFile();
                    $contractFileModel->save($file);
                }
                // 提交事务
                Db::commit();
                return $result;
            } catch (Exception $e) {
                Db::rollback();
                return $e->getMessage();
            }
        }
        $user = Session::get('admin');
        // get contract categories
        $categories = Category::all();
        $customers = Customer::all();
        $this->assign([
            'title' => '添加新合同',
            'categories' => $categories,
            'customers' => $customers,
            'user' => $user
        ]);
        return $this->fetch();
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $contract = new ContractModel();
            // 检测合同编号是否已存在
            $one = $contract->where('contract_sn', 'eq', $post['contract_sn'])->find();
            if (!$one) {
                return -1; // 合同不存在
            }
            $post['short_sn'] = explode('-', $post['contract_sn'])[2];
            $post['contract_date'] = strtotime($post['contract_date']);
            $post['due_date'] = strtotime($post['due_date']);
            // 获取附加项数据
            if (isset($post['extra'])) {
                $extra = $post['extra'];
                unset($post['extra']);
            }
            // 获取上传数据
            if (isset($post['contract_file'])) {
                $file = $post['contract_file'];
                unset($post['contract_file']);
            }
            Db::startTrans();
            try{
                $result = $contract->allowField(true)->isUpdate(true)->save($post);
                // 区分修改的附加项与新增的
                if (isset($extra)) {
                    $updateData = $addData = [];
                    foreach ($extra as $item) {
                        if (isset($item['id'])) {
                            $updateData[] = $item;
                        } else {
                            $item['contract_id'] = $contract->id;
                            $addData[] = $item;
                        }
                    }
                    $extraModel = new ContractExtra;
                    if (!empty($updateData)) {
                        $extraModel->isUpdate(true)->saveAll($updateData);
                    }
                    if (!empty($addData)) {
                        $extraModel->isUpdate(false)->saveAll($addData);
                    }

                }
                // 区分更新与新增
                if (isset($file)) {
                    $contractFileModel = new ContractFile();
                    if (isset($file['id']) && !empty($file['id'])) {
                        $contractFileModel->isUpdate(true)->save($file);
                    } else {
                        $file['contract_id'] = $contract->id;
                        $contractFileModel->isUpdate(false)->save($file);
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
        $user = Session::get('admin');
        $categories = Category::all();
        $customers = Customer::all();
        $contract = ContractModel::with('file,extra')->find($id);
        $types = Type::where('category_id', 'eq', $contract->category_id)->select();
        $linkmans = Linkman::where('customer_id', 'eq', $contract->customer_id)->select();

        // 验证合同状态，审核中，审核通过，都不能修改
        // todo...

        $this->assign([
            'title' => '合同修改',
            'categories' => $categories,
            'customers' => $customers,
            'contract' => $contract,
            'types' => $types,
            'linkmans' => $linkmans,
            'user' => $user
        ]);
        return $this->fetch();
    }
    // list
    public function all()
    {
        $contracts = ContractModel::with('category,type,customer,linkman,file,extra')->order('id desc')->select();
//        $contracts = json_encode($contracts);
        // 获取筛选数据
        $customers = Customer::all();
        $types = Type::all();
        $categories = Category::all();
        $this->assign([
            'title' => '合同管理',
            'icon' => '<span class="icon-contract icon"></span>',
            'contracts' => $contracts,
            'customers' => $customers,
            'categories' => $categories,
            'types' => $types

        ]);
        return $this->fetch();
    }
    //delete
    public function del()
    {
        if (!$this->request->isDelete()) {
            return 'error';
        }
        $id = $this->request->param('id');
        if (!is_numeric($id) && !is_int($id)) {
            return 'error';
        }

        // 是否处于审批中
        $one = Approval::where('contract_id', $id)->where('start', 1)->find();
        if ($one) {
            return '该合同在审批中';
        }


        Db::startTrans();
        try{
            // 删除附件
            $files = ContractFile::where('contract_id', 'eq', $id)->field('id')->select();
            if ($files) {
                foreach ($files as $file) {
                    ContractFile::destroy($file['id']);
                }
            }
            // 删除附加项
            $extras = ContractExtra::where('contract_id', 'eq', $id)->field('id')->select();
            if ($extras) {
                foreach ($extras as $extra) {
                    ContractExtra::destroy($extra['id']);
                }
            }
            $result = ContractModel::destroy($id);
            // 提交事务
            Db::commit();
            return $result;
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return $result;
    }

    // expiring
    public function expiring()
    {
        // 30天内到期的
        $contracts = ContractModel::with('category,type,customer,linkman,file,extra')
            ->where('due_date', '<', strtotime("+1 month"))
            ->order('id desc')
            ->select();
        foreach ($contracts as &$item) {
            $days = ceil(($item['due_date'] - time()) / (3600 * 24));
            $item['days'] = $days;
        }
        if ($this->request->isAjax()) {
            return json($contracts);
        }
        $this->assign([
            'title' => '即将到期的合同',
            'contracts' => $contracts
        ]);
        return $this->fetch('all');
    }

    // 获取合同编号
    public function getContractSn()
    {
        if (!$this->request->isAjax()) {
            return -1;
        }
        $categoryId = $this->request->param('category_id');
        $typeId = $this->request->param('type_id');
        $maxSn = ContractModel::withTrashed()->where('category_id', 'eq', $categoryId)
            ->where('type_id', 'eq', $typeId)
            ->max('short_sn');
        // 自增1
        $maxSn ++;
        // 如果位数小于3，则左侧补0
        if (strlen($maxSn) < 2) {
            $maxSn = str_pad($maxSn, 2, '0', STR_PAD_LEFT);
        }
        return $maxSn;
    }

    // search
    public function search()
    {
        if (!$this->request->isAjax() && !$this->request->isPost()) {
            return '非法操作';
        }
        $post = $this->request->post();
        print_r($post);
    }
}