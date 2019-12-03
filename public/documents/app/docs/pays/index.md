#### 支付方式列表

- 描述：`GET`   [/pays](http://app-dev.mikwine.com/api/pays)

| 参数       | 数据类型   | 是否必传 | 含义     | 备注        |

```json
[
  {
    "id": 1,
    "pay_way": "alipay",    // 支付方式【alipay: 支付宝支付， wechat:微信支付， balance: 余额支付， line：线下支付】
    "is_default": 0         // 是否默认【0：否， 1：是】
  }
]
```