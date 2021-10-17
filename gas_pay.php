<?php

require_once __DIR__ . '/QixiPay.php';

$config = [
    /* 网关地址 */
    'url' => 'http://pay.fax-checker.com',
    /* 金额 */
    'money' => 1,
    /* 后台 -> 对接设置 -> ID + Token */
    'id' => 8,
    'token' => 'xxx',
    /* 异步通知的地址，填写您自己的 */
    'notifyUrl' => 'http://www.baidu.com/index.php?id=1',
];

$pay = new QixiPay($config['token']);
$pay->requestUrl = $config['url'];
$pay->id = $config['id'];
$pay->tradeNo = uniqid();
$pay->name = 'Test';
$pay->notifyUrl = $config['notifyUrl'];

$response = $pay->gasPay($config['money']);

if ( isset($response['code']) && 1 === $response['code'] )
{
    header("Location: {$response['url']}");
}
else
{
    echo '<pre>';
    print_r($response);
    echo '<pre>';
}
