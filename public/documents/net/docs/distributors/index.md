#### 经销商列表

- 描述：`GET`   [/distributors](http://app-dev.mikwine.com/net/distributors)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |

```json
[
    {
        "id": 1,
        "name": "经销商voluptatem",                    // 经销商名称
        "level": 2,                                   // 级别
        "parent_distributor_id": 0,                   // 上级经销商ID
        "area_id": 110106,                            // 地区ID
        "address": "",                                // 详细地址
        "contact": "",                                // 联系人
        "telephone": "",                              // 联系号码
<<<<<<< .mine
        "deleted_at": null,                           //状态:null为启用 禁用data格式2017-02-01 15:00:38
        "created_at": "2017-02-01 15:00:38",          //状态:null为启用 禁用data格式2017-02-01 15:00:38
||||||| .r1406
        "deleted_at": null,
        "created_at": "2017-02-01 15:00:38",
=======
        "deleted_at": null,			      //状态:null为启用 禁用data格式2017-02-01 15:00:38
        "created_at": "2017-02-01 15:00:38",
>>>>>>> .r1424
        "updated_at": "2017-02-01 15:00:39"
    }
]
```