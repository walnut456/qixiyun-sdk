<?php

class QixiPay
{
    /**
     * 请求地址
     * @var string
     */
    protected $requestUrl;

    protected $id;

    protected $openId;

    /**
     * 订单号
     * @var string
     */
    protected $tradeNo;

    protected $name;

    protected $mode;

    /**
     * 回调地址
     * @var string
     */
    protected $notifyUrl;
    
    /**
     * 同步跳转地址
     * @var string
     */
    protected $returnUrl;

    protected $token;

    // 初始化
    public function __construct ( $token = '' )
    {
        date_default_timezone_set('PRC');

        if ( ! empty( $token ) ) {
            $this->token = $token;
        }
    }

    // get
    public function __get ($key)
    {
        return $this->$key;
    }

    // set
    public function __set ($key,$value)
    {
        $this->$key = $value;
    }

    /**
     * 下单
     * @param  string $amount      
     * @param  string $paymentCode 
     * @return mixed
     * {"res":{"appId":"wx490ff7cae27a94ed","timeStamp":"1572884237","nonceStr":"d0rOm4NR5dy5Qp4R","package":"prepay_id=wx0500171774453850c3386ec41071946800","signType":"MD5","paySign":"5ADED4560C1303CE4C77619A1DBAF6E1"},"name":"纪念日百货","money":0.01}
     */
    public function unify ( $money = '0.01' )
    {
        $data = [];

        $data['id']         = $this->id;
        $data['openid']    = $this->openId;
        $data['trade_no']   = $this->tradeNo;
        $data['notify_url'] = $this->notifyUrl;
        $data['name']       = $this->name;
        $data['money']      = $money;
        
        $sign = $this->makeSign($data);
        $data['sign'] = $sign;
        $data['sign_type'] = 'MD5';

        $params = http_build_query( $data );
        $url = $this->requestUrl . '/api/do_jsapi.html' . '?' . $params;

        list($body, $err) = $this->getCurl( $url );
        if ( $body )
        {
            $ret = json_decode($body, true);
            return $ret;
        }
        else
        {
            return [false, $err];
        }
    }

    /**
     * Invoke UnionPay
     */
    public function unionPay($money = '0.01')
    {
        $data = [];

        $data['id']         = $this->id;
        $data['trade_no']   = $this->tradeNo;
        $data['notify_url'] = $this->notifyUrl;
        $data['name']       = $this->name;
        $data['money']      = $money;
        
        $sign = $this->makeSign($data);
        $data['sign'] = $sign;
        $data['sign_type'] = 'MD5';

        $params = http_build_query( $data );
        $url = $this->requestUrl . '/api/unionpay.html' . '?' . $params;

        list($body, $err) = $this->getCurl( $url );
        if ( $body )
        {
            $ret = json_decode($body, true);
            return $ret;
        }
        else
        {
            return [false, $err];
        }
    }

    /**
     * Call common payment
     */
    public function callCommonPay($money = 1)
    {
        $data = [];
        $data['id']           = $this->id;
        $data['out_trade_no'] = $this->tradeNo;
        $data['notify_url']   = $this->notifyUrl;
        $data['name']         = $this->name;
        $data['money']        = $money;
        $data['mode']         = $this->mode;
        if ($this->returnUrl)
        {
            $data['return_url'] = $this->returnUrl;
        }
        
        $sign = $this->makeSign($data);
        $data['sign'] = $sign;
        $data['sign_type'] = 'MD5';

        $params = http_build_query( $data );
        $url = $this->requestUrl . '/api/commonpay.html' . '?' . $params;

        list($body, $err) = $this->getCurl( $url );
        if ( $body )
        {
            $ret = json_decode($body, true);
            return $ret;
        }
        else
        {
            return [false, $err];
        }
    }

    public function verify( $data )
    {
        if (!isset($data['sign']) || !$data['sign']) {
            return false;
        }
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['sign_type']);
        $sign2 = $this->makeSign( $data );

        if ($sign != $sign2) {
            return false;
        }
        return true;
    }

    /**
     * 生成签名
     * @param  array $params 
     * @param  string $apiKey
     * @return string    
     */
    public function makeSign ($data = [])
    {
        $data = array_filter($data);
        if (get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }
        ksort($data);
        $str1 = '';
        foreach ($data as $k => $v) {
            $str1 .= '&' . $k . "=" . $v;
        }
        $str = $str1 . $this->token;
        $str = trim($str, '&');
        $sign = md5($str);

        return $sign;
    }

    /**
     * 获取openid
     * @param  string $callback
     * @return string              
     */
    public function getOpenId ($callback)
    {
        if (isset($_GET['openid'])) {
            return $_GET['openid'];
        }
        $url = $this->requestUrl . "/api/get_openid.html" . '?' . http_build_query([
            'id'       => $this->id,
            'callback' => $callback
        ]);
        header("Location:" . $url);exit;
    }

    /**
     * 循环遍历
     * @param  array &$array 
     * @return mixed        
     */
    public function each(&$array){
       $res = array();
       $key = key($array);
       if($key !== null) {
           next($array); 
           $res[1] = $res['value'] = $array[$key];
           $res[0] = $res['key'] = $key;
       } else {
           $res = false;
       }
       return $res;
    }

    private function getCurl($url, $post = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            return [false, $err];
        } else {
            return [$ret, null];
        }
    }
}