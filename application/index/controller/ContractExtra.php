<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/21
 * Time: 11:50
 */

namespace app\index\controller;

use app\index\model\ContractExtra as ContractExtraModel;

class ContractExtra extends BaseController
{
    public function del()
    {
        if (!$this->request->isDelete()) {
            return 'error';
        }
        $id = $this->request->param('id');
        if (!is_numeric($id) && !is_int($id)) {
            return 'error';
        }
        $result = ContractExtraModel::destroy($id);
        return $result;
    }
}