<?php


namespace app\index\controller;

use app\index\model\Customer as CustomerModel;

class Customer extends BaseController
{
    // add
    public function add()
    {
        if ($this->request->isAjax() && $this->request->isPost()) {
            $post = $this->request->post();
            $customer = new CustomerModel();
            // 检测客户是否已存在
            $one = $customer->where('name', 'eq', $post['name'])->find();
            if ($one) {
                return -1; // 客户存在
            }
            $result = $customer->allowField(true)->save($post);
            return $result;
        }
        $this->assign([
            'title' => '新增客户'
        ]);
        return $this->fetch('add');
    }
    // edit
    public function edit()
    {
        $id = $this->request->param('id');
        if ($this->request->isPost() && $this->request->isAjax()) {
            $post = $this->request->post();
            $customer = new CustomerModel();
            $result = $customer->save($post, ['id' => $id]);
            return $result;
        }
        $customer = CustomerModel::get($id);
        $this->assign([
            'title' => '客户修改',
            'customer' => $customer
        ]);
        return $this->fetch();
    }

    // delete
    public function del()
    {
        if (!$this->request->isDelete()) {
            return 'error';
        }
        $id = $this->request->param('id');
        if (!is_numeric($id) && !is_int($id)) {
            return 'error';
        }
        $result = CustomerModel::destroy($id);
        return $result;
    }
    // list
    public function all()
    {
        $customers = CustomerModel::all();
        $customers = json_encode($customers);
        $this->assign([
            'title' => '客户管理',
            'customers' => $customers
        ]);
        return $this->fetch();
    }

}