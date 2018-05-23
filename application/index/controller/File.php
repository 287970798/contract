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

    public function importExcel()
    {
        vendor("phpexcel.PHPExcel.IOFactory");
        $fileName = ROOT_PATH . 'public/student.xlsx';

        /**
         *  加载所有sheet
         */
        // $obj = \PHPExcel_IOFactory::load($fileName);

        /**
         *  加载指定sheet
         */
        // 获取文件类型
        $fileType = \PHPExcel_IOFactory::identify($fileName);
        // 创建读取文件的操作对象
        $reader = \PHPExcel_IOFactory::createReader($fileType);
        // 加载指定sheet
        $reader->setLoadSheetsOnly('Sheet1');
        // 加载文件
        $obj = $reader->load($fileName);

        // 字段 以列号作为key
        $fields = ['A' => 'name', 'B' => 'phone'];
        // 数据存储临时数组
        $data = $rowData = $cellData = [];
        // 迭代sheet
        foreach ($obj->getWorksheetIterator() as $sheet) {
            // 迭代row
            foreach ($sheet->getRowIterator() as $row) {
                // 过滤第一行
                if ($row->getRowIndex() < 2) continue;
                // 迭代cell
                foreach ($row->getCellIterator() as $key => $cell){
                    // 获取当前行的cell的值
                    $cellData[$fields[$key]] =$cell->getValue();
                }
                // 将当前行中存储有所有cell值的数组赋值给行数组
                $rowData[] = $cellData;
                // 清空当前行的cell数据存储数组，以用于存储下一行的数据
                $cellData = [];
            }
            // 将当前sheet的所有行的数据，存储到data数组中
            $data[] = $rowData;
            // 清空当前sheet的行数据数组，以用于存储下个sheet的数据
            $rowData = [];
        }
        print_r($data);
    }
}