<?php 

require_once __DIR__ . '/QixiPay.php';

$config = [
    /* 网关地址 */
    'url'    => 'http://pay.k64x.cn',
    /* 金额 */
    'amount' => '0.01',
    /* 后台 -> 对接设置 -> ID + Token */
    'id'     => 8,
    'token'  => 'xxxxxx',
    /* 异步通知的地址，填写您自己的 */
    'notifyUrl' => ''
];

/* 在 jsapi 支付的时候需要用户的 openid 来下单，需要提前获取
 * API：[GET] http://pay.k64x.cn/pay/openid
 */
$openId = '';

$pay = new QixiPay( $config['token'] );
$pay->requestUrl = $config['url'] . '/api/do_jsapi.html';
$pay->id         = $config['id'];
$pay->openId     = $openId;
$pay->tradeNo    = order_sn();
$pay->name       = 'Test';
$pay->notifyUrl  = $config['notifyUrl'];

$ret = $pay->unify( $config['amount'] );

$contents = <<<EOF
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script>
        function onBridgeReady(){
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest', {
                "appId": "{$ret['res']['appId']}", //公众号appid
                "timeStamp": "{$ret['res']['timeStamp']}", //时间戳，自1970年以来的秒数
                "nonceStr": "{$ret['res']['nonceStr']}", //随机串
                "package": "{$ret['res']['package']}",
                "signType": "{$ret['res']['signType']}",
                "paySign": "{$ret['res']['paySign']}" //微信签名
            },
            function(res){
                if(res.err_msg == "get_brand_wcpay_request:ok" ){
                    alert('支付成功');
                }
            });
        }
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        }else{
            onBridgeReady();
        }
    </script>
</head>
<body>

</body>
</html>
EOF;

echo $contents;