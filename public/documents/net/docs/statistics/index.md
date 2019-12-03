#### 营销员总业绩

- 描述：`GET`   [/statistics/sales](http://app-dev.mikwine.com/net/statistics/sales?salesman_id=1,4)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | string | 是    | 营销员ID   | 多个用英文状态下的逗号隔开【例如：1,4】 |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": [
        {
            "salesman_id": "6",     // 销售员ID
            "shop_count": 2,        // 终端数量
            "scan_count": 104,      // 扫码总数量
            "user_count": 8,        // 服务员数量
            "scan_money": 18543,    // 扫码总金额
            "sales_money": 465      // 销售总金额
        }
    ]
}
```

#### 根据营销员ID查询时间段业绩

- 描述：`GET`   [/statistics/sales_count](http://app-dev.mikwine.com/net/statistics/sales_count?salesman_id=5,6&start_date=2018-01-01&end_date=2018-02-02)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | string | 是    | 营销员ID   | 多个用英文状态下的逗号隔开【例如：1,4】 |
| start_date | date | 是    | 开始时间   | 【2018-01-04】 |
| end_date | date | 是    | 结束时间   | 【2018-01-05】 |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   |  |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": [
        {
            "scan_count": 0,        // 扫码总数量
            "scan_money": 0,        // 扫码总金额
            "sales_money": 0        // 销售总金额（使用时除以100）
            "salesman_id": "5"      // 营销员ID
        }
    ]
}
```

#### 根据营销员ID查询时间段滞销终端数量

- 描述：`GET`   [/statistics/sales_date_count](http://app-dev.mikwine.com/net/statistics/sales_date_count?salesman_id=5&type=query&start_date=2018-05-20&end_date=2018-05-21)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| type | string | 是    | 查询类型   | 【query：查询, export：导出】 |
| salesman_id | string | 是    | 营销员ID   | 多个用英文状态下的逗号隔开【例如：1,4】 |
| start_date | date | 是    | 开始时间   | 【2018-05-20】 |
| end_date | date | 是    | 结束时间   | 【2018-05-21】 |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   |  |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": {
        "total": 1,                         // 总条数
        "data": [
            {
                "total_shop_count": 17,         // 合作终端数量
                "scan_shop_count": 0,           // 扫码终端数量
                "no_scan_shop_count": 17,       // 滞销终端数量
                "total_user_count": 17,         // 服务员数
                "scan_count": 0,                // 扫码数
                "scan_user_count": 0,           // 扫码服务员数
                "statics_date": "2018-05-21",   // 日期
                "salesman_id": "1"              // 销售员ID
            }
        ],
        "statics": {
            "total_shop_count": "20",           // 合作终端数量总和
            "scan_shop_count": 0,               // 扫码终端数量总和
            "no_scan_shop_count": 0,           // 滞销终端数量总和
            "total_user_count": "20",          // 服务员数总和
            "scan_count": 0,                    // 扫码数总和
            "scan_user_count": 0                // 扫码服务员数总和
        }
    }
}

```

#### 营销员查询时间段业绩

- 描述：`GET`   [/statistics/sales/section](http://app-dev.mikwine.com/net/statistics/sales/section?salesman_id=1,4&start_date=2018-01-15&end_date=2018-01-18)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | int | 是    | 营销员ID   |  |
| start_date | date | 是    | 开始时间   | 【2018-01-04】 |
| end_date | date | 是    | 结束时间   | 【2018-01-05】 |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": [
        {
            "date": "2018-01-15",   // 日期
            "shop_count": 0,        // 终端数量
            "total_user_count": 0,  // 合作终端数量总和
            "scan_count": 0,        // 扫码总数量
            "user_count": 0,        // 服务员数量
            "total_user_count": 0,  // 合作服务员数量总和
            "scan_money": 0,        // 扫码总金额
            "sales_money": 0,        // 销售总金额
            "salesman_id": 24
        }
    ]
}
```


#### 营销员销售报表

- 描述：`GET`   [/statistics/sales_date_percent_count](http://app-dev.mikwine.com/net/statistics/sales_date_percent_count?end_date=2018-09-04&start_date=2018-08-29&type=query&salesman_id=1,2,3&page=1&pageSize=20)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| type | string | 是    | 查询类型   | 【query：查询, export：导出】 |
| salesman_id | string | 是    | 营销员ID   | 多个用英文状态下逗号隔开【1,2,3】 |
| start_date | date | 是    | 开始时间   | 【2018-09-01】 |
| end_date | date | 是    | 结束时间   | 【2018-09-03】 |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 【默认20条】 |

```json
{
    "Code": 200,
    "Msg": "查询成功！",
    "Info": {
        "total": 3,                             // 总条数
        "data": [
            {
                "statics_date": "2018-09-01",   // 日期
                "scan_count": 0,                // 扫码数量
                "scan_count_percent": 0,        // 与昨日环比（扫码数）使用时除以100为百分比
                "total_shop_count": 0,          // 总计合作终端（统计启用状态）
                "shop_count": 0,                // 新增合作终端（统计启用状态）
                "scan_shop_count": 0,           // 扫码终端数（扫过码的终端数）
                "no_scan_shop_count": 0,        // 滞销终端（没有扫码的）
                "total_user_count": 0,          // 总服务员数
                "user_count": 0,                // 新增服务员数
                "scan_user_count": 0,           // 扫码服务员数
            }
        ]
    }
}

```

#### 营销员下终端扫码数

- 描述：`GET`   [/statistics/scans](http://app-dev.mikwine.com/net/statistics/scans?salesman_id=5&start_date=2017-03-07%2000:00:00&end_date=2018-03-19%2023:59:59)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| type | string | 是    | 查询类型   | 【query：查询, export：导出】 |
| salesman_id | string | 否    | 营销员ID   | 多个用英文状态下的逗号隔开【例如：5,11】 |
| start_date | datetime | 否    | 开始时间   |  |
| end_date | datetime | 否    | 结束时间   |  |
| shop_name | string | 否    | 终端名称   |  |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 【默认20条】 |

```json
{
  "Code": 200,
  "Msg": "查询成功！",
  "Info": {
    "total": 21,
    "data": [
      {
        "shop_id": 197,           // 终端ID
        "shop_name": "dn",        // 终端名称
        "salesman_name": "忘川",  // 销售员名称
        "scan_count": 0,           // 扫码数
        "salesman_id": 5,           // 销售员ID
      }
    ],
    "statics": {
        "scan_count": 0          // 扫码数总和
    }
  }
}
```

#### 营销员扫码数-小程序

- 描述：`GET`   [/statistics/get_scan_count_by_salesman_id](http://app-dev.mikwine.com/net/statistics/get_scan_count_by_salesman_id?salesman_id=24)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | string | 是    | 营销员ID   |  |
| start_date | datetime | 是    | 开始时间   | Y-m-d H:i:s |
| end_date | datetime | 是    | 结束时间   | Y-m-d H:i:s |

```json
{
	"Code": 200,
	"Msg": "查询成功！",
	"Info": {
		"salesman_id": 24,                  // 营销员ID
		"scan_count": 38,                   // 扫码数
		"salesman_name": "血银"             // 营销员名称
	}
}
```


#### 营销员终端扫码数-小程序

- 描述：`GET`   [/statistics/get_scan_count_by_filter](http://app-dev.mikwine.com/net/statistics/get_scan_count_by_filter)
- 状态码：200/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| salesman_id | string | 否    | 营销员ID   |  |
| shop_name | string | 否    | 终端名称   |  |
| start_date | datetime | 否    | 开始时间   | Y-m-d H:i:s |
| end_date | datetime | 否    | 结束时间   | Y-m-d H:i:s |
| page | int | 否    | 页码   |  |
| pageSize | int | 否    | 每页数量   | 【默认20条】 |

```json
{
	"Code": 200,
	"Msg": "查询成功！",
	"Info": {
	    "data": [{
            "salesman_id": 5,                   // 营销员ID
            "shop_id": 161,                     // 终端ID
            "scan_count": 1,                    // 扫码数
            "salesman_name": "忘川",            // 营销员名称
            "shop_name": "烈火青春"             // 终端名称
        }],
        "statics": {
            "scan_count": 1                     // 扫码数总和
        }
	}
}
```
