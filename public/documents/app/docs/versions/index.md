#### App版本升级

- 描述：`GET`   [/app/versions?type=ios](http://app-dev.mikwine.com/api/app/versions?type=ios)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| type | string | 是    | 平台   | 【ios：IOS平台  android：安卓平台】 |

```json
{
    "id": 2,
    "version": "2.2",								// 版本号
    "type": "ios",									// 平台
    "description": "这是ios2.2版本",				 // 版本描述
    "download_url": "https://www.baidu.com/ios",	// 下载地址
    "version_code": "",								// 版本二维码
    "is_force_update": "no",						// 强制升级【yes：是 no：否】
    "created_at": "2018-01-02 10:20:33",
    "updated_at": "2018-01-02 13:39:07",
    "deleted_at": null
}
```