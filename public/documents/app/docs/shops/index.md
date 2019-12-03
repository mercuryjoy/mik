#### 终端列表

- 描述：`GET`   [/shops](http://app-dev.mikwine.com/api/shops?page=2&pageSize=10)

| 参数       | 数据类型   | 是否必传 | 含义     | 备注        |
| -------- | ------ | ---- | ------ | --------- |
| q        | string | 否    | 搜索关键词  | 【终端名称】    |
| page     | int    | 否    | 页码     |           |
| pageSize | int    | 否    | 每页显示条数 | 【默认10条/页】 |

```json
[
    {
        "id": 149,
        "name": "暗室逢灯大师傅撒",				  // 终端名称
        "level": "A",							// 级别
        "distributor_id": 1,					// 经销商ID
        "area_id": 1000010,						// 地区代码
        "address": "北京市东城区",			   // 地址
        "salesman_id": 1,						// 销售员ID
        "logo": "",								// 终端logo
        "owner_id": 1,							// 店长ID
        "contact_person": "fsd",				// 联系人
        "contact_phone": "18895300085",			// 联系号码
        "category_id": 1,						// 餐饮类别ID
        "per_consume": "1.00"					// 人均消费
        "deleted_at": null,
        "created_at": "2017-12-29 17:38:17",
        "updated_at": "2017-12-29 18:32:19",
    }
]
```