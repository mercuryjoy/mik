#### 营销员订单列表

- 描述：`GET`   [/goods/orders/sales](http://app-dev.mikwine.com/net/goods/orders/sales?salesman_id=1,4)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | string | 否    | 营销员ID   | 多个用英文状态下的逗号隔开【例如：1,4】【此参数和shop_id必有一个要传】 |
| shop_id | int | 否    | 终端ID   | 【此参数和salesman_id必有一个要传】 |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 【默认20条】 |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": {
        "total": 6,                                           // 订单总数量
        "data": [
            {
                "id": 206,                                    // 订单ID
                "user_id": 157,
                "item_id": 129,
                "amount": 2,                                  // 数量
                "shipping_address": "重庆 重庆 九龙坡, 关注骨",  // 收货地址
                "status": "finished",                         // 订单状态【established：订单成立， created：待发货， shipped：已发货， canceled：已取消， finished：已完成, drawback: 取消订单待审核中】
                "remarks": "热",
                "deleted_at": null,
                "created_at": "2018-01-17 16:32:16",          // 订单创建ID
                "updated_at": "2018-01-17 19:48:01",
                "type": "purchase",
                "contact_name": "品格",                         // 收货联系人
                "contact_phone": "18516771960",               // 收货联系电话
                "money": 2050,                                // 订单金额【除以100后单位为元】
                "is_pay": 1,                                  // 是否支付【false：未支付，true：已支付】
                "pay_way": "line",                            // 支付方式【balance：余额，alipay：支付宝，wechat：微信，line：线下】
                "is_checked": 1,                              // 财务是否审核【false：未审核，true：已审核】
                "salesman_id": 5,                             // 营销员ID
                "distributor_id": 106,
                "shop_id": 161,
                "shop": {
                    "id": 161,
                    "name": "烈火青春",                       // 采购商家名称
                    "level": "A",
                    "distributor_id": 106,
                    "area_id": 110100,
                    "address": "宜山路1068号",
                    "salesman_id": 5,
                    "deleted_at": null,
                    "created_at": "2017-12-29 14:24:31",
                    "updated_at": "2018-01-26 20:30:08",
                    "logo": "/uploads/shop_logo/99d17ad97bb3aad36bcf41606f53f946.png",
                    "owner_id": 207,
                    "contact_person": "河图",
                    "contact_phone": "18516771960",
                    "category_id": 2,
                    "per_consume": "102.00",
                    "source": 0,
                    "net_shop_id": null
                }
            }
        ]
    }
}
```