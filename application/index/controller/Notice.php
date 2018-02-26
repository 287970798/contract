<?php


namespace app\index\controller;


use think\Session;
use app\index\model\ApprovalNotice;

class Notice extends BaseController
{
    // add
    public static function add(Array $data)
    {
        $model = $data['model'];
        if (!class_exists($data['model'])) {
            $model = 'app\\index\\model\\'.$model;
        }
        if (!class_exists($model)) {
            return false;
        }
        $model = new $model;
        $result = $model->allowField(true)->save($data);
    }
    // update

    // del
    public function del()
    {
        if ($this->request->isAjax() && $this->request->isDelete()) {
            $ids = $this->request->param('ids');
            $result = ApprovalNotice::destroy($ids);
            return $result;
        }
    }

    // list
    public function pull()
    {
        $userId = Session::get('admin.id');
        $notices = $this->approvalNotice($userId);
        $count = count($notices);
        return ['notices'=>$notices, 'count'=>$count];
    }

    // approval notice
    public function approvalNotice($id)
    {
        $notices = ApprovalNotice::with(['approval'=>function($query){$query->field('id,name');}])->where('user_id', $id)->select();
        return $notices;
    }

}