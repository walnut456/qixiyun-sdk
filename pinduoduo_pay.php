<?php
/* Warning: 请在手机浏览器打开访问此页面，PC 浏览器无效 */
require_once __DIR__ . '/QixiPay.php';

$config = [
    /* 网关地址 */
    'url' => 'http://api.zhifubaopay.com',
    /* 支持的金额 1 5 10 15 20 50 */
    'money' => 1,
    /* 后台 -> 对接设置 -> ID + Token */
    'id' => 8,
    'token' => 'xxx',
    /* 2: 拼多多支付 */
    'mode'  => 2,
    /* 异步通知的地址，填写您自己的 */
    'notifyUrl' => 'http://www.baidu.com/index.php?id=1',
    /* 支付完成之后同步返回的地址，填写您自己的 */
    'returnUrl' => 'http://www.baidu.com',
];

$pay = new QixiPay($config['token']);
$pay->requestUrl = $config['url'];
$pay->id = $config['id'];
$pay->tradeNo = uniqid();
$pay->name = 'Test';
$pay->mode = $config['mode'];
$pay->notifyUrl = $config['notifyUrl'];
$pay->returnUrl = $config['returnUrl'];

$response = $pay->callCommonPay($config['money']);

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