<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/21
 * Time: 2:39
 */

namespace app\index\controller;


class File extends BaseController
{
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = $this->request->file('file');
        $code = 1;
        $msg = '';
        $ext = '';
        $src = '';
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                $ext = $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $src = $info->getSaveName();
                $code = 0;
            }else{
                // 上传失败获取错误信息
                $msg = $file->getError();
            }
        } else {
            $msg = '没有文件被上传';
        }
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => [
                'ext' => $ext,
                'src' => $src
            ],
        ];
        return json($result);
    }
}