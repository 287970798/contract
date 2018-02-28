<?php


namespace app\index\lib\enum;


class ContractStatusEnum
{
    // 新录
    const UNUSED = 0;
    // 待审
    const WAITING = 1;
    // 审批中
    const APPROVALING = 2;
    // 驳回
    const REJECT = 3;
    // 过审
    const PASS = 4;
    // 执行中
    const EXECUTING = 5;
    // 中止
    const CANCEL = 6;
    // 续期
    const RENEWED = 7;
    // 结束
    const FINISHED = 8;

}