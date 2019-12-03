#### 手机号验证码登录

- 描述：`POST`   [/login](http://app-dev.mikwine.com/api/login)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| telephone | string | 是    | 手机号   |  |
| code | string | 是    | 验证码   |  |

```json
{
    "id": 207,
    "name": "紫苏",                    // 服务员名称
    "gender": "female",               // 性别
    "telephone": "18895300085",       // 手机号
    "shop_id": 161,                   // 终端ID
    "status": "normal",               // 状态【normal: 正常， pending: 待审核】
    "money_balance": 571,             // 账户余额【使用时除以100，单位元】
    "point_balance": 22,              // 账户积分
    "wechat_openid": null,            // 微信OpenId
    "deleted_at": null,
    "created_at": "2018-01-06 19:15:32",
    "updated_at": "2018-01-06 22:50:48",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIwNywiaXNzIjoiaHR0cDpcL1wvbWlrLndvcmtcL2FwaVwvbG9naW4iLCJpYXQiOjE1MTU3MjY3NDUsImV4cCI6MTU0NzI2Mjc0NSwibmJmIjoxNTE1NzI2NzQ1LCJqdGkiOiJjMDkwYTRiODdmY2YxNmIzNWNiMDBkMTJhM2JmMzRmOSJ9.PMeIfzdG0J__jbozx6YYU1TrR-S3FUD5blrEfGQ5ZDo",
    "is_owner": true                  // 是否是店长 【false:否， true：是】
}
```
