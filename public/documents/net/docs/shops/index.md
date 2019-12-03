#### 终端列表

- 描述：`GET`   [/shops](http://app-dev.mikwine.com/net/shops)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   |  |

```json
[{
	"id": 205,
	"name": "5",
	"level": "D",
	"distributor_id": null,
	"area_id": 110000,
	"address": "但是发到付",
	"salesman_id": 0,
	"deleted_at": null,
	"created_at": "2018-01-11 19:00:57",
	"updated_at": "2018-01-11 19:00:57",
	"logo": "",
	"owner_id": 0,
	"contact_person": "",
	"contact_phone": "",
	"category_id": 0,
	"per_consume": "0.00"
}]
```
#### 新建终端 && 修改终端

- 描述：`POST`   [/shops](http://app-dev.mikwine.com/net/shops)
- 状态码：200/400/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| id | int | 否    | PHP终端ID   | 修改时传 |
| net_shop_id | int | 是    | net终端ID   | 新增时传 |
| name | string | 是    | 终端名称   |  |
| distributor_id | int | 否    | 经销商ID   |  |
| area_id | int | 是    | 地区ID   |  |
| address | string | 是    | 地址   |  |
| salesman_id | int | 是    | 销售员ID   |  |
| contact_phone | string | 是    | 联系电话   |  |
| category_id | int | 是    | 餐饮类别ID   |  |
| per_consume | float | 是    | 人均消费   |  |
| logo | string | 是    | 终端展示图   |  |

```json
{
    "Code": 200,          // 【200：成功，400失败,403：报错信息】
    "Msg": "终端创建成功",
    "Info": {
        "id": 212
    }
}
```

#### 检查终端名称是否存在
   
   - 描述：`GET`   [/shops/check](http://app-dev.mikwine.com/net/shops/check)
   - 状态码：200/403/400
   
   | 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
   | ---- | ------ | ---- | ---- | ------------------------- |
   | shop_name | string | 是    | 终端名称   |  |
   | address | string | 否    | 地址   | 【暂不判断，可不传】 |
   | distributor_name | string | 否    | 经销商名称   | 【暂不判断，可不传】 |
   
```json
{
   "Code": 200,
   "Msg": "终端不存在",
   "Info": null
}
```

#### 通过终端ID查询详情
   
   - 描述：`GET`   [/shops/show](http://app-dev.mikwine.com/net/shops/show?shop_id=1,2,3)
   - 状态码：200/403
   
   | 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
   | ---- | ------ | ---- | ---- | ------------------------- |
   | shop_id | string | 是    | 终端ID   | 多个用英文状态下的逗号隔开【例如：1,2】 |
   
```json
{
    "Code": 200,
    "Msg": "查询成功",
    "Info": [
        {
            "id": 1,                              // 终端ID
            "name": "米客测试终端浙江",              // 终端名称
            "area_id": 330100,                    // 终端地区代码
            "address": "浙江省杭州江干区解放东路58号",// 终端地址
            "contact_person": "2",                // 联系人
            "contact_phone": "15921414715",       // 联系电话
            "per_consume": "20.00",               // 人均消费
            "area": {
                "display": "浙江 杭州"             // 地区名称
            },
            "distributor": {
                "name": "经销商voluptatem"        // 经销商名称
            },
            "category": {
                "name": "米客米酒"               // 经销商名称
            },
            "salesman": {
                "id": 1,
                "name": "黄芳"                  // 销售员
            }
        }
    ]
}
```