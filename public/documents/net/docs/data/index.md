#### 同步终端数据

- 描述：`GET`   [/data/link/](http://app-dev.mikwine.com/net/data/link/)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| startTime | string | 是    | 开始时间   |  |
| endTime | string | 是    | 结束时间   |  |

```json
[
    {
        "id": 213,
        "name": "sfdas",
        "level": "A",
        "distributor_id": 1,
        "area_id": 500103,
        "address": "123123213",
        "salesman_id": 1,
        "deleted_at": null,
        "created_at": "2018-01-23 15:05:34",
        "updated_at": "2018-01-23 15:05:34",
        "logo": "",
        "owner_id": 0,
        "contact_person": "123",
        "contact_phone": "123",
        "category_id": 2,
        "per_consume": "999.99",
        "source": 0,
        "net_shop_id": null,
        "data_type": "add",       // 数据类型【add：新增 update：更新 delete：删除】
        "category": {
            "id": 2,
            "parent_id": 0,
            "name": "小吃",
            "level": 1,
            "created_at": "2017-12-15 11:55:17",
            "updated_at": "2017-12-15 11:55:17",
            "deleted_at": null
        }
    }
]
```

#### 同步地区数据

- 描述：`GET`   [/data/areas/](http://app-dev.mikwine.com/net/data/areas/)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |

```json
[{
	"id": 110000,
	"name": "北京",
	"parent_id": null,
	"grandparent_id": null,
	"display": "北京",
	"deleted_at": null,
	"created_at": "2017-02-01 15:00:30",
	"updated_at": "2017-02-01 15:00:30"
}]
```

#### 同步餐饮类别数据

- 描述：`GET`   [/data/categories/](http://app-dev.mikwine.com/net/data/categories/)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |

```json
[{
	"id": 1,
	"parent_id": 0,
	"name": "米客米酒",
	"level": 1,
	"created_at": "2017-12-15 11:55:14",
	"updated_at": "2017-12-17 13:08:36",
	"deleted_at": null
}]
```