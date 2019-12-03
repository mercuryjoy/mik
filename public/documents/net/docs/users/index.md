#### 修改服务员状态信息

- 描述：`POST`   [/users](http://app-dev.mikwine.com/net/users/user_id=213&type=status)
- 状态码：200/400/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| user_id | string | 是    | 服务员ID   | 多个用英文状态下的逗号隔开【例如：213，214】 |
| type | string | 是    | 修改类型   | 【status：审核状态， active：用户帐号状态， deleted：删除】 |
| value | int | 否    | 状态值   | 当type为active时必传【0：禁用，1：启用】 |

```json
{
    "Code": 200,                // 所有修改操作返回200才代表成功，400代表失败
    "Msg": "用户信息更新成功",
    "Info": null
}
```

#### 服务员详情

- 描述：`GET`   [/users](http://app-dev.mikwine.com/net/users?id=213)
- 状态码：200/400/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| id | int | 是    | 服务员ID   |  |

```json
{
    "Code": 200,
    "Msg": "用户信息查询成功",
    "Info": {
        "id": 128,                       // 服务员ID
        "salesman_id": 5,                // 销售员ID
        "name": "陈现在",                 // 名称
        "gender": "male",                // 性别【male：男，female：女】
        "telephone": "15926504054",      // 号码
        "status": "normal",              // 审核状态【pending：未审核，normal：已审核】
        "active": 1,                     // 帐号启用【0：禁用，1：启用】
        "created_at": "2017-11-03 16:33:25", // 注册时间
        "shop_name": "烈"                // 所属终端
    }
}
```

#### 修改服务员基本信息

- 描述：`POST`   [/users/profile](http://app-dev.mikwine.com/net/users/users/profile?id=214&telephone=18745682254&gender=male&name=wtas)
- 状态码：200/400/403

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| id | int | 是    | 服务员ID   |  |
| telephone | string | 是    | 手机号   |  |
| gender | string | 是    | 性别   | 【male：男，female：女】 |
| name | string | 是    | 名称   |  |

```json
{
    "Code": 200,                // 所有修改操作返回200才代表成功，400代表失败
    "Msg": "用户信息更新成功",
    "Info": null
}
```