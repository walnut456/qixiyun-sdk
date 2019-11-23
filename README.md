### 参数准备
登录后台 -> 对接设置 -> ID + Token

### 获取用户 openid

在jsapi支付的时候需要用户的openid来下单，所以，在您下单之前，请使用本接口获取用户的openid。

**[GET]**  http://pay.k64x.cn/api/get_openid.html

| Params | Remarks |
| ------------ | ------------ |
| id | 对接ID，从后台获取 |
| callback | 您的业务URL，需要 url_encode， 会带回 openid 参数，下一步发起支付会用到  |

Example：
```
http://pay.k64x.cn/api/get_openid.html?id=1&callback=http%3a%2f%2ftest.com
```