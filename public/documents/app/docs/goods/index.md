#### 采购商品列表

- 描述：`GET`   [/goods/items](http://app-dev.mikwine.com/api/goods/items)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 默认15条 |

```json
[
  {
    "id": 100,                                                        
    "name": "商品100",                                                 // 名称
    "description": "好长的描述描述好长的描述描述",                         // 描述
    "price_money": 1000,                                              // 价格【使用时除以100为真实价格，单位：元】
    "stock": 1038,                                                    // 库存
    "photo_url": "http://app-dev.mikwine.com/storepics/1513220023.jpg"// 图片
  }
]
```

#### 店长下采购订单

- 描述：`POST`   [/goods/orders](http://app-dev.mikwine.com/api/goods/orders)
- 状态码：600/601/602/604/605：报错信息。成功返回订单详情

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| item_id | int | 是    | 商品ID   |  |
| amount | int | 是    | 数量   | 大于等于1 |
| contact_name | string | 是    | 收货人   |  |
| contact_phone | string | 是    | 联系号码   |  |
| shipping_address 	 | string | 是    | 收货地址   | 不能少于5个字符 |

```json
{
    "user_id": 207,
    "item_id": "101",
    "amount": "1",                                                // 购买数量
    "money": 200,                                                 // 订单总价
    "shipping_address": "上海市闵行区宜山路100号",                   // 收货地址
    "status": "created",                                          // 订单状态【established：订单成立， created：待发货， shipped：已发货， canceled：已取消， finished：已完成, drawback: 取消订单待审核中】
    "remarks": null,                                              // 备注
    "type": "purchase",                                           // 订单类型【purchase: 采购订单， exchange: 兑换订单】
    "contact_name": "小王",                                        // 联系人
    "contact_phone": "18895300085",                               // 联系电话
    "pay_way": null,                                              // 支付方式【balance：余额，alipay：支付宝，wechat：微信，line：线下】
    "is_pay": false,                                              // 是否支付【false：未支付，true：已支付】
    "is_checked": false,                                          // 财务是否审核【false：未审核，true：已审核】
    "updated_at": "2018-01-12 15:37:51",
    "created_at": "2018-01-12 15:37:51",
    "id": 156,                                                    // 订单ID
    "alipay_param": "alipay_sdk=alipay-sdk-php-20161101&amp;app_id=2018012302042945&amp;biz_content=%7B%22body%22%3A%22%E9%87%87%E8%B4%AD%E8%AE%A2%E5%8D%95269%22%2C%22subject%22%3A+%22%E6%B5%8B%E8%AF%95%E9%87%87%E8%B4%AD%E5%95%86%E5%93%811%22%2C%22out_trade_no%22%3A+%22269%22%2C%22timeout_express%22%3A+%2230m%22%2C%22total_amount%22%3A+%220.1025%22%2C%22product_code%22%3A%22QUICK_MSECURITY_PAY%22%7D&amp;charset=UTF-8&amp;format=json&amp;method=alipay.trade.app.pay&amp;notify_url=%E5%95%86%E6%88%B7%E5%A4%96%E7%BD%91%E5%8F%AF%E4%BB%A5%E8%AE%BF%E9%97%AE%E7%9A%84%E5%BC%82%E6%AD%A5%E5%9C%B0%E5%9D%80&amp;sign_type=RSA2&amp;timestamp=2018-01-25+10%3A12%3A17&amp;version=1.0&amp;sign=razBW%2Fwq4uH%2B8INPKRR1qK3YHnkTeMEv01A0VTZeuBSqbbUcBtqPQdts9FiEx4WQNcWHIv6WVdorqQx8BeYWPELDqdmMiJUwd6hQDZZQBEs3%2FvXwVD%2BbhmUAeUXqlFbNT0lgGOU1Ll0HVqr2%2B3tAYj%2BDDarJ8aM1ELpNzFFHgZSz%2Fe4iahblywU3ju4CNZM1xC6AYLDIHBmppjnu9sxS%2BoJlEThmlyvV8rTwv4IIW79H3GdQU3XhkKzupFdb2Afx6%2FZgmWlk%2FCIjGeMh0YcTwLszX3pFouu6XrAi99mZKArF9slB%2BEYhXw3JBoNTDcFT9cXTyAijFiqXkmquROmI0g%3D%3D",
    "item": {                                                     // 商品详情
        "id": 101,
        "name": "商品101",                                         // 商品名称
        "description": "好长的描述描述",                             // 描述
        "price_money": 10,                                         // 价格
        "price_point": 0,
        "is_virtual": 0,
        "stock": 232,                                               // 库存
        "photo_url": "http://app-dev.mikwine.com/storepics/1513220023.jpg", // 图片
        "status": "in_stock",                                               // 状态【in_stock：库存足够，out_of_stock：库存不足，deleted：已下架】
        "deleted_at": null,
        "created_at": "2018-01-10 15:43:55",
        "updated_at": "2018-01-12 15:37:51",
        "type": "purchase"                                                   // 商品类型【purchase: 采购商品， exchange: 兑换商品】
    }
}
```

#### 获取店长所有采购订单

- 描述：`GET`   [/goods/orders](http://app-dev.mikwine.com/api/goods/orders)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| status | string | 否    | 订单状态   | 【created: 未发货,shipped：已发货,canceled：已取消,drawback：取消订单待审核,finished：已完成】 |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 默认15条 |

```json
[
    {
        "id": 156,                                                      // 订单ID
        "user_id": 207,
        "item_id": 101,
        "amount": 1,                                                    // 商品数量
        "money": 200,                                                   // 订单总价
        "shipping_address": "上海市闵行区一串路100号",                     // 收货地址
        "status": "shipped",                                            // 订单状态【established：订单成立， created：待发货， shipped：已发货， canceled：已取消， finished：已完成, drawback: 取消订单待审核中】
        "remarks": null,                                                // 订单备注
        "deleted_at": null,
        "created_at": "2018-01-12 15:37:51",                            // 下订单时间
        "updated_at": "2018-01-12 15:37:51",
        "type": "purchase",                                            // 订单类型【purchase：采购订单， exchange：兑换订单】
        "contact_name": null,                                          // 联系人
        "contact_phone": null,                                         // 联系电话
        "is_pay": 1,                                                   // 是否支付【1：已支付， 0：未支付】
        "pay_way": "alipay",                                           // 支付方式【alipay：支付宝支付， wechat：微信支付， balance：账户余额支付， line：线下支付】
        "is_checked": 1,                                               // 财务是否审核【1：已审核， 0：未审核】
        "show_pay_button": false,                                      // 是否显示支付按钮【true：显示， false：不显示】
        "show_cancel_button": false,                                   // 是否显示取消按钮【true：显示， false：不显示】
        "item": {
            "id": 101,
            "name": "商品101",                                          // 商品名称
            "description": "好长的描述",                                 // 商品描述
            "price_money": 10,                                         // 商品单价
            "price_point": 0,
            "is_virtual": 0,
            "stock": 232,
            "photo": "http://mik.work/storepics/1513220023.jpg",      // 商品图片
            "status": "in_stock",
            "deleted_at": null,
            "created_at": "2018-01-10 15:43:55",
            "updated_at": "2018-01-12 15:37:51",
            "type": "purchase"
        }
    }
]
```
#### 获取采购订单详情

- 描述：`GET`   [/goods/orders/show](http://app-dev.mikwine.com/api/goods/orders/show)
- 状态码：604：报错信息。成功返回订单详情

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| order_id | int | 是    | 订单ID   |  |

```json
{
    "id": 156,                                                      // 订单ID
    "user_id": 207,
    "item_id": 101,
    "amount": 1,                                                    // 商品数量
    "money": 200,                                                   // 订单总价
    "shipping_address": "上海市闵行区一串路100号",                     // 收货地址
    "status": "shipped",                                            // 订单状态【established：订单成立， created：待发货， shipped：已发货， canceled：已取消， finished：已完成, drawback: 取消订单待审核中】
    "remarks": null,                                                // 订单备注
    "deleted_at": null,
    "created_at": "2018-01-12 15:37:51",                            // 下订单时间
    "updated_at": "2018-01-12 15:37:51",
    "type": "purchase",                                            // 订单类型【purchase：采购订单， exchange：兑换订单】
    "contact_name": null,                                          // 联系人
    "contact_phone": null,                                         // 联系电话
    "is_pay": 1,                                                   // 是否支付【1：已支付， 0：未支付】
    "pay_way": "alipay",                                           // 支付方式【alipay：支付宝支付， wechat：微信支付， balance：账户余额支付， line：线下支付】
    "is_checked": 1,                                               // 财务是否审核【1：已审核， 0：未审核】
    "show_pay_button": false,                                      // 是否显示支付按钮【true：显示， false：不显示】
    "show_cancel_button": false,                                   // 是否显示取消按钮【true：显示， false：不显示】
    "item": {
        "id": 101,                                                 // 商品ID
        "name": "商品101",                                          // 商品名称
        "description": "好长的描述",                                 // 商品描述
        "price_money": 10,                                         // 商品单价
        "price_point": 0,
        "is_virtual": 0,
        "stock": 232,
        "photo_url": "http://mik.work/storepics/1513220023.jpg",    // 商品图片
        "status": "in_stock",
        "deleted_at": null,
        "created_at": "2018-01-10 15:43:55",
        "updated_at": "2018-01-12 15:37:51",
        "type": "purchase"
    }
}
```

#### 店长取消采购订单

- 描述：`PUT`   [/goods/orders/cancel](http://app-dev.mikwine.com/api/goods/orders/cancel)
- 状态码：403：报错信息。成功返回订单详情

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| order_id | int | 是    | 订单ID   |  |

```json
{
    "id": 156,                                                      // 订单ID
    "user_id": 207,
    "item_id": 101,
    "amount": 1,                                                    // 商品数量
    "money": 200,                                                   // 订单总价
    "shipping_address": "上海市闵行区一串路100号",                     // 收货地址
    "status": "drawback",                                           // 订单状态【established：订单成立， created：待发货， shipped：已发货， canceled：已取消， finished：已完成, drawback: 取消订单待审核中】
    "remarks": null,                                                // 订单备注
    "deleted_at": null,
    "created_at": "2018-01-12 15:37:51",                            // 下订单时间
    "updated_at": "2018-01-12 15:37:51",
    "type": "purchase",                                            // 订单类型【purchase：采购订单， exchange：兑换订单】
    "contact_name": null,                                          // 联系人
    "contact_phone": null,                                         // 联系电话
    "is_pay": 1,                                                   // 是否支付【1：已支付， 0：未支付】
    "pay_way": "alipay",                                           // 支付方式【alipay：支付宝支付， wechat：微信支付， balance：账户余额支付， line：线下支付】
    "is_checked": 1,                                               // 财务是否审核【1：已审核， 0：未审核】
    "show_pay_button": false,                                      // 是否显示支付按钮【true：显示， false：不显示】
    "show_cancel_button": false,                                   // 是否显示取消按钮【true：显示， false：不显示】
    "item": {
        "id": 101,                                                 // 商品ID
        "name": "商品101",                                          // 商品名称
        "description": "好长的描述",                                 // 商品描述
        "price_money": 10,                                         // 商品单价
        "price_point": 0,
        "is_virtual": 0,
        "stock": 232,
        "photo_url": "http://mik.work/storepics/1513220023.jpg",    // 商品图片
        "status": "in_stock",
        "deleted_at": null,
        "created_at": "2018-01-10 15:43:55",
        "updated_at": "2018-01-12 15:37:51",
        "type": "purchase"
    }
}
```

#### 根据支付方式生成订单密钥

- 描述：`POST`   [/goods/orders/generate](http://app-dev.mikwine.com/api/goods/orders/generate)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| order_id | int | 是    | 订单ID   |  |
| pay_way_id | int | 是    | 支付方式ID   |  |

```json
{
    "alipay_sdk": "alipay-sdk-php-20161101",
    "notify_url": "http://app-dev.mikwine.com/api/goods/orders/notifications",      // 回调地址
    "app_id": "2018012302042945",                                                   // APPID
    "biz_content": "{\"body\":\"好长的描述描述\",\"subject\": \"测试采购商品1\",\"out_trade_no\": \"266\",\"timeout_express\": \"30m\",\"total_amount\": \"1025\",\"product_code\":\"QUICK_MSECURITY_PAY\"}",
    "charset": "utf-8",
    "format": "json",
    "method": "alipay.trade.app.pay",                                               // 支付包支付接口
    "timestamp": "2018-01-26 14:15:30",
    "version": "1.0",
    "sign_type": "RSA2",
    "sign": "p0/IHTLpzPZdcigkcTiEw6tNV3OsbjD60estl+0uS9iVWtL22xsg5bMapxzySLGvFXIhj2i7OYfeJ1HLWufeDO47kH5xsr9y5wW77zYxWoECrhe7FkgHv9+4cYqqSiPfRJeUC/XRK5EBiDUU1FpnPP1wOo6IYn+TxejEwIK895gy2CLw/5+jBCSW0fG1HIdZP4ZBS7ZnJWpEY/AZ5O2ODON/OM1Ute/kYX53PEGFMtX2lbTczueZKIHhp6F+iEBvW8Rr4ryA50lCrKNXqesF+6Y2xSNje8iPsSy3ywv3wlG1y+L1ktH6NRr2482wgRfxa3Tk9xPr1uCH9UAQnwqmWQ=="
}
```