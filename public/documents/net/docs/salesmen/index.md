#### 销售员列表

- 描述：`GET`   [/salesmen](http://app-dev.mikwine.com/net/salesmen)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |

```json
[
  {
    "id": 1,
    "name": "黄芳",                   // 营销员名称
    "phone": "13641659353",          // 营销员手机号
    "deleted_at": null,
    "created_at": "2017-03-21 13:39:24",
    "updated_at": "2017-03-21 13:39:24"
  }
]
```

#### 通过销售员ID查询所属服务员

- 描述：`GET`   [/salesmen/users](http://app-dev.mikwine.com/net/salesmen/users)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | string | 否    | 营销员ID   | 不传为所有服务员，多个用英文状态下的逗号隔开【例如：1,5】 |
| user_name | string | 否    | 服务员名称   |  |
| shop_name | string | 否    | 终端名称   |  |
| telephone | string | 否    | 服务员手机号   |  |
| status | string | 否    | 服务员状态【normal:正常， pending：待审核】   |  |
| start_date | datetime | 否    | 服务员注册开始时间   |  |
| end_date | datetime | 否    | 服务员注册结束时间   |  |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 【默认20条】 |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": {
        "total": 6,                               // 总数量
        "data": [
            {
                "id": 128,                       // 服务员ID
                "salesman_id": 5,                // 销售员ID
                "name": "陈现在",                 // 名称
                "gender": "male",                // 性别【male：男，female：女】
                "telephone": "15926504054",      // 号码
                "status": "normal",              // 审核状态【pending：未审核，normal：已审核】
                "created_at": "2017-11-03 16:33:25", // 注册时间
                "shop_name": "烈"                // 所属终端
            }
        ]
    }
}
```

#### 新增销售员

- 描述：`POST`   [/salesmen](http://app-dev.mikwine.com/net/salesmen)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| name | string | 是    | 营销员姓名   | 1-15位中英文字符 |
| phone | string | 是    | 服务员手机号   |  |
| status | int | 是    | 状态   | 【0：禁用， 1：启用】 |

```json
{
    "Code": 200,
    "Msg": "销售员新增成功！",
    "Info": {
        "name": "wt",
        "phone": "18895300085",
        "status": 1,
        "updated_at": "2018-03-06 16:25:39",
        "created_at": "2018-03-06 16:25:39",
        "id": 6
    }
}
```

#### 修改销售员

- 描述：`POST`   [/salesmen/update](http://app-dev.mikwine.com/net/sa/update)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| id | integer | 是    | 营销员ID   | 正整数 |
| name | string | 是    | 营销员姓名   | 1-15位中英文字符 |
| phone | string | 是    | 服务员手机号   |  |
| status | int | 是    | 状态   | 【0：禁用， 1：启用】 |

```json
{
    "Code": 200,
    "Msg": "销售员修改成功！",
    "Info": null
}
```
