<?php

require_once __DIR__ . '/QixiPay.php';

$config = [
    'token'  => 'xxxxxx'
];

$data = $_GET;

/* sign 验证 */
$pay = new QixiPay( $config['token'] );
/* 如果有携带其他自定义的参数，请 unset 掉 */
// unset( $data['xxx'] );
$checkResult = $pay->verify( $data );
if ( $checkResult )
{
    echo 'success';
}
else
{
    echo 'error';
}