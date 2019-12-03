#### 用户扫码

- 描述：`GET`   [/users/scans](http://app-dev.mikwine.com/net/users/scans)
- 状态码：200/400/403/

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| code | string | 是    | 二维码   |  |
| user_id | int | 是    | 用户ID   |  |
| user_name | string | 是    | 用户名称   |  |
| net_wechat_unionid | string | 是    | 微信unionid   |  |

```json
{
	"Code": 200,
	"Msg": "您是老用户,二维码可用，扫码成功",
	"Info": null
}
```