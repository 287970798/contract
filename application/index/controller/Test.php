<?php


namespace app\index\controller;


use think\Cache;

class Test
{
    /**
     * 测试通过curl调用接口发送邮件 测试成功
     * @return int|mixed
     */
    public function mail()
    {
        $url = 'http://uniteedu.cn/mail/recevie-data.php?project=lcr';
        $arr = [
            'email' => 'yyloon@126.com',
            'desc' => 'this is desc',
            'url' => 'http://baidu.com'
        ];
        $res = httpCurl($url, 'post', '', $arr);
        return $res;
    }

    public function cache()
    {
        $user_id =5;
        $data = [
            'id'=>1
        ];
        Cache::set('approve',$data);
        dump(Cache::get('approve'));
    }
}