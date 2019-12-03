#### 手机号加验证码登录

- 描述：`POST`   [/login](http://app-dev.mikwine.com/api/login?telephone=18895300085&code=8888)

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| telephone | string | 是    | 手机号   |  |
| code | string |  是    | 验证码   |  |

```json
{
  "id": 257,
  "name": "18895300085",
  "gender": null,
  "telephone": "18895300085",
  "shop_id": 1,                     【终端ID,如为空，则跳转终端选择页面】
  "status": "pending",
  "money_balance": 0,
  "point_balance": 0,
  "wechat_openid": null,            // 微信OpenId
  "deleted_at": null,
  "created_at": "2018-06-21 19:14:34",
  "updated_at": "2018-06-21 19:14:34",
  "wechat_unionid": null,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjI1NywiaXNzIjoiaHR0cDpcL1wvbWlrLndvcmtcL2FwaVwvdjJcL2xvZ2luIiwiaWF0IjoxNTI5NTgwNzg0LCJleHAiOjE1NjExMTY3ODQsIm5iZiI6MTUyOTU4MDc4NCwianRpIjoiZTFhMTc2ZmEwMWIzOGUyMDhiMDEyNTIwNDg2YmExNDIifQ.fVSRbsKF0IpRefm4Y-SL4Iu1J-5sx4uOt3Ib5-K3FGQ",
  "is_owner": false
}
```

#### 发送验证码-V2

- 描述：`POST`   [/sms/code](http://app-dev.mikwine.com/api/sms/code?telephone=18895300085&type=verify_register_password)
- 状态码：
```tex
101|手机号码不能为空
102|手机号码格式不正确
103|验证码类型错误
200|短信发送成功
400|短信发送失败

当type为verify_reset_password时会有如下状态码（账号找回密码发送的验证码）
104|您的账号未注册，请去注册
105|您的账号已被禁用，请联系管理员
```

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| telephone | string | 是    | 手机号   |  |
| type | string |  是    | 类型   | 【verify_register：原手机验证码登录注册，verify_register_password：注册验证码, verify_reset_password：密码重置验证码, 'wechat_bind'：微信绑定,'wechat_unbind'：微信解绑,'withdraw'：提现,'update_login_password'：更新登录密码,'update_withdraw_password'：更新提现密码】 |

```json
{
  "code": 200,
  "message": "短信发送成功"
}
```

#### 验证码验证-V2

- 描述：`POST`   [/sms/code/check_auth](http://app-dev.mikwine.com/api/sms/code/check_auth?type=wechat_bind&code=4602)
- 状态码：
```tex
201|验证类型不能为空
202|验证类型不正确
203|验证码不能为空
204|验证码为4位数字
205|您暂未绑定手机号，请绑定
206|未找到验证码
207|验证码不正确
208|验证码已过期
200|验证成功
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |
| type | string | 是    | 验证码类型  |  【'wechat_bind'：微信绑定,'wechat_unbind'：微信解绑,'withdraw'：提现,'update_login_password'：更新登录密码,'update_withdraw_password'：更新提现密码】    |
| code | string | 是    | 验证码  |    |

```json
{
  "code": 200,
  "message": "验证码正确"
}
```

#### 重置密码验证码验证-V2

- 描述：`POST`   [/sms/code/check_verify_reset_password](http://app-dev.mikwine.com/api/sms/code/check_verify_reset_password?telephone=18895300085&code=4602)
- 状态码：
```tex
201|手机号码不能为空
202|手机号码不正确
203|验证码不能为空
204|验证码为4位数字
205|未找到验证码
206|验证码不正确
207|验证码已过期
200|验证成功
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |
| telephone | string | 是    | 手机号  |     |
| code | string | 是    | 验证码  |    |

```json
{
  "code": 200,
  "message": "验证码正确"
}
```

#### 账号密码注册-V2

- 描述：`POST`   [/v2/register](http://app-dev.mikwine.com/api/v2/register?telephone=18895300085&code=2942&password=wangtao19930817&password_confirmation=wangtao19930817)
- 状态码：
```tex
201|手机号码不能为空
201|手机号码不能为空
202|手机号码格式不正确
203|验证码不能为空
204|验证码为4位数字
205|密码不能为空
206|只能由字母和数字组成
207|密码最少为8个字符
208|密码最多为20个字符
209|密码和确认密码不一致
210|确认密码不能为空
211|该手机号已注册,不能重复注册
212|该手机号已被禁用,请换一个重试
213|未找到验证码
214|验证码不正确
215|验证码已过期
216|注册失败
```

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| telephone | string | 是    | 手机号   |  |
| code | string |  是    | 验证码   |  |
| password | string |  是    | 密码   | 字母和数字组合，8-20个字符 |
| password_confirmation | string |  是    | 验证密码   | 字母和数字组合，8-20个字符 |

```json
{
    "name": "18895300085",            // 服务员名称
    "telephone": "18895300085",       // 手机号
    "updated_at": "2018-01-06 22:50:48",
    "created_at": "2018-01-06 19:15:32",
    "id": 207,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIwNywiaXNzIjoiaHR0cDpcL1wvbWlrLndvcmtcL2FwaVwvbG9naW4iLCJpYXQiOjE1MTU3MjY3NDUsImV4cCI6MTU0NzI2Mjc0NSwibmJmIjoxNTE1NzI2NzQ1LCJqdGkiOiJjMDkwYTRiODdmY2YxNmIzNWNiMDBkMTJhM2JmMzRmOSJ9.PMeIfzdG0J__jbozx6YYU1TrR-S3FUD5blrEfGQ5ZDo",
}
```

#### 账号密码登录-V2

- 描述：`POST`   [/v2/account_login](http://app-dev.mikwine.com/api/v2/account_login?telephone=18895300085&password=test8888ssdfsadfasds)
- 状态码：
```tex
201|手机号码不能为空
202|手机号码格式不正确
203|该手机号未注册,请注册
204|密码不能为空
205|该手机号已被禁用,请换一个重试
206|该账号未设置密码，请设置密码后重试
207|密码错误
208|您未选择所属终端,请联系管理员
209|您的所属终端已被禁用,请联系管理员
```

| 参数   | 数据类型   | 是否必传 | 含义   | 备注                        |
| ---- | ------ | ---- | ---- | ------------------------- |
| telephone | string | 是    | 手机号   |  |
| password | string |  是    | 密码   |  |

```json
{
    "id": 207,
    "name": "紫苏",                   // 服务员名称
    "gender": "female",               // 性别【male：女，female：男】
    "telephone": "18895300085",       // 手机号
    "shop_id": 161,                   // 终端ID【终端ID,如为空，则跳转终端选择页面】
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

#### 重置登录密码
   
- 描述：`PUT`   [/reset_account_password](http://app-dev.mikwine.com/api/reset_account_password?telephone=18895300085&password=test123456&password_confirmation=test123456)
- 状态码：
```tex
201|手机号不能为空
202|手机号码格式不正确
202|该手机号码未注册，请注册
203|密码不能为空
204|只能由字母和数字组成
205|密码为6个数字组成
206|密码和确认密码不一致
207|确认密码不能为空
200|密码重置成功
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |
| telephone | string | 是    | 手机号  |    |
| password | string | 是    | 密码  |   字母和数字组合8-20个字符   |
| password_confirmation | string | 是    | 确认密码  |   和密码保持一致   |

```json
{
 "code": 200,
 "message": "密码重置成功"
}
```

#### 修改登录或提现密码

- 描述：`PUT`   [/users/@me/set_password](http://app-dev.mikwine.com/api/users/@me/set_password?type=reset_pay_password&password=123456&password_confirmation=123456)
- 状态码：
```tex
201|类型不能为空
202|类型值不正确
203|密码不能为空
204|确认密码不能为空
205|只能由字母和数字组成
206|密码最少为8个字符
207|密码最多为20个字符
208|密码和确认密码不一致
209|只能由字母和数字组成
210|密码为6个数字组成
211|密码和确认密码不一致
212|密码不能与原密码相同
213|密码修改失败
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |
| type | string | 是    | 修改类型  |   【reset_login_password：修改登录密码，reset_pay_password：修改提现密码】   |
| password | string | 是    | 密码  |   【type为reset_login_password时，为字母和数字组合8-20个字符、type为reset_pay_password时，为6个数字】   |
| password_confirmation | string | 是    | 确认密码  |   和密码保持一致   |
   
```json
   {
     "id": 257,
     "name": "test23",
     "gender": null,
     "telephone": "18895300085",
     "shop_id": 1,
     "status": "pending",
     "money_balance": 0,
     "point_balance": 0,
     "wechat_openid": null,
     "deleted_at": null,
     "created_at": "2018-06-21 19:14:34",
     "updated_at": "2018-06-25 17:23:58",
     "wechat_unionid": null,
     "wechat_name": null,
     "wechat_avatar": null
   }
```

#### 查询服务员信息（刷新账号审核状态）

- 描述：`GET`   [/users/@me](http://app-dev.mikwine.com/api/users/@me)

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |

```json
{
  "id": 257,
  "name": "test23",
  "gender": null,
  "telephone": "18895300085",
  "shop_id": 1,             //【终端ID,如为空，则跳转终端选择页面】
  "status": "pending",      // 账号状态【pending：待审核， normal：正常】
  "money_balance": 0,		// 账户余额（使用时除以100，单位为元）
  "point_balance": 0,
  "wechat_openid": null,		// 微信OpenID（绑定微信标识，为null则未绑定）
  "deleted_at": null,
  "created_at": "2018-06-21 19:14:34",
  "updated_at": "2018-06-22 16:33:19",
  "wechat_unionid": null,
  "wechat_name": null,		    // 绑定微信名称
  "wechat_avatar": null,		// 绑定微信头像
  "is_owner": false,
  "is_set_pay_password": 1,  // 是否设置支付密码【1：已设置， 0：未设置】
}
```

#### 微信绑定

- 描述：`PUT`   [/users/@me](http://app-dev.mikwine.com/api/users/@me)
- 状态码：
```tex
401|姓名为2-30位中英文字符
402|性别输入有误
403|终端未找到
405|手机号码格式不正确
406|手机号码已被其他服务员使用
407|您已绑定过微信账号，请解绑后重试
408|该微信号已绑定其他账号
400|不支持修改终端
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |
| name | string | 否    | 昵称  |      |
| gender | string | 否    | 性别  |  【male:男性，female：女性】    |
| shop_id | int | 否    | 终端ID  |      |
| telephone | string | 否    | 手机号  |      |
| wechat_openid | string | 否    | 微信openid  |    |
| wechat_unionid | string | 否    | 微信unionid  |      |
| wechat_name | string | 否    | 微信昵称  |    |
| wechat_avatar | string | 否    | 微信头像地址  |    |

```json
{
  "id": 257,
  "name": "test23",
  "gender": null,
  "telephone": "18895300085",
  "shop_id": 1,
  "status": "pending",
  "money_balance": 0,
  "point_balance": 0,
  "wechat_openid": null,
  "deleted_at": null,
  "created_at": "2018-06-21 19:14:34",
  "updated_at": "2018-06-22 16:33:19",
  "wechat_unionid": null
}
```

#### 微信解绑

- 描述：`PUT`   [/users/@me/unbind_wechat](http://app-dev.mikwine.com/api/users/@me/unbind_wechat?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjI2MiwiaXNzIjoiaHR0cDpcL1wvYXBwLWRldi5taWt3aW5lLmNvbVwvYXBpXC9sb2dpbiIsImlhdCI6MTUzMDA2NjgzMiwiZXhwIjoxNTYxNjAyODMyLCJuYmYiOjE1MzAwNjY4MzIsImp0aSI6IjI0ZjJjNWYyMTU2NmU3ZjhlNGJiZTQ1NjhiZDAzZjU5In0.4yro7l8iE5zMdK3DrbXSBzsCZnzq0T3ZcbVd87SrkHg&wechat_openid=)
- 状态码：
```tex
201|您暂未绑定微信
202|解绑失败
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |

```json
{
  "id": 257,
  "name": "test23",
  "gender": null,
  "telephone": "18895300085",
  "shop_id": 1,
  "status": "pending",
  "money_balance": 0,
  "point_balance": 0,
  "wechat_openid": null,
  "deleted_at": null,
  "created_at": "2018-06-21 19:14:34",
  "updated_at": "2018-06-22 16:33:19",
  "wechat_unionid": null
}
```

#### 提现（添加支付密码验证）

- 描述：`POST`   [/withdraw](http://app-dev.mikwine.com/api/withdraw?amount=100&password=123456)
- 状态码：
```tex
400|钱包提现功能暂时无法使用
411|提现金额不能为空
412|提现金额需大于等于1元,小于等于200元
413|钱包余额不足
414|未关联微信
415|微信支付失败,稍后重试
416|密码不能为空
417|密码必须为6个数字
418|密码错误
419|您暂未设置提现密码
```

| 参数        | 数据类型   | 是否必传 | 含义   | 备注   |
| --------- | ------ | ---- | ---- | ---- |
| amount | integer | 是    | 金额  |   范围100-20000   |
| version | string | 是    | 版本号  |      |
| pay_password | integer | 是    | 密码  |   6个数字   |

```json
{
  "id": 257,
  "type": "withdraw",
  "amount": -100,
  "user_id": 5,
  "comment": 'Withdraw, Wechat Payment Bill Number:,
  "created_at": "2018-06-21 19:14:34",
  "updated_at": "2018-06-25 17:23:58",
}
```
