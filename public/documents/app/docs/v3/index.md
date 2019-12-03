#### 查询销售员信息根据salesman_id:

- 描述：`GET`   [/salesmen](http://app-dev.mikwine.com/api/salesmen?salesman_id=1)

| 参数        | 数据类型   | 是否必传 | 含义     | 备注        |
| --------    | ---------- | -------- | -------- | ----------- |
| salesman_id | int        | 是       | 销售员id |             |

```json
    {
      "id": 1,
      "name": "wt",               //销售员姓名
      "phone": "11111111111",     //联系电话
      "deleted_at": null,         //删除时间
      "created_at": "2017-03-21 13:39:24",
      "updated_at": "2018-03-28 20:39:04",
      "status": 1                 //状态：[0:禁用，1:启用]'
    }
```
#### 查询销售员信息根据shop_id:

- 描述：`GET`   [/salesmen](http://app-dev.mikwine.com/api/salesmen/shopmen?shop_id=240)

| 参数        | 数据类型   | 是否必传 | 含义     | 备注        |
| --------    | ---------- | -------- | -------- | ----------- |
| shop_id     | int        | 是       | 经销商id |             |

```json
{
    "id": 22,
    "name": "欧阳",
    "phone": "15107115058",
    "deleted_at": null,
    "created_at": "2018-03-19 15:53:35",
    "updated_at": "2018-03-19 15:53:35",
    "status": 1
}
```

#### 互动消息:

- 描述：`GET`   [/replies/replyshow](http://app-dev.mikwine.com/api/replies/replyshow?user_id=220)

| 参数        | 数据类型   | 是否必传 | 含义     | 备注        |
| --------    | ---------- | -------- | -------- | ----------- |
| user_id     | int        | 是       | 用户id   |             |

```json
[
    {
        "id": 12,
        "user_id": 220,
        "content": "错别字有点多啊，兄弟",
        "deleted_at": null,
        "created_at": "2018-02-06 15:13:59",
        "updated_at": "2019-07-16 17:02:42",
        "status": "reply",
        "replies": [
            {
                "id": 3,
                "feedback_id": 12,
                "user_id": 220,
                "content": "もしも",
                "created_at": "2019-07-24 16:23:59",
                "updated_at": "2019-07-19 18:41:16"
            }
        ]
    },
    {
        "id": 11,
        "user_id": 220,
        "content": "考虑考虑",
        "deleted_at": null,
        "created_at": "2017-12-26 17:51:06",
        "updated_at": "2019-07-16 17:02:46",
        "status": "no",
        "replies": []
    }
]
```

#### 店长取消采购订单:

- 描述：`PUT`   [/goods/orders/canceled](http://app-dev.mikwine.com/api/goods/orders/canceled?order_id=594)

| 参数        | 数据类型   | 是否必传 | 含义     | 备注        |
| --------    | ---------- | -------- | -------- | ----------- |
| order_id    | int        | 是       | 订单id   |             |

```json
'code' => 200, 'message' => '取消订单成功'
'code' => 403, 'message' => '订单异常，请重试'
```
#### 通知消息

- 描述：`GET`   [/news](http://app-dev.mikwine.com/api/news?user_id=1)

| 参数        | 数据类型   | 是否必传 | 含义     | 备注        |
| --------    | ---------- | -------- | -------- | ----------- |
| user_id     | int        | 是       | 用户id   |             |

```json
[
 
    {
        "id": 23,
        "title": "测试用例啥的",                       //通知标题
        "thumbnail_url": "/newspics/1563963852.png",//内容图片
        "content_url": "http://mik.work/admin/news",//url
        "created_at": "2019-07-24 18:24:12",
        "updated_at": "2019-07-24 18:24:12",
        "audio_url": null,  
        "status": "normal",
        "picture_url": "",              //通知缩略图
        "content": "zz",              //内容
        "newslog": "unread"       //unread已读,read未读
    },
    {
        "id": 22,
        "title": "随机",
        "thumbnail_url": "/newspics/1563963804.png",
        "content_url": "http://mik.work/admin",
        "created_at": "2019-07-19 16:51:50",
        "updated_at": "2019-07-24 18:23:24",
        "audio_url": "/uploads/packages/1563526310.wmv",
        "status": "normal",
        "picture_url": "",
        "content": "xx",
        "newslog": "read"
    },
    {
        "id": 2,
        "title": "test2",
        "thumbnail_url": "/newspics/1544681765.jpg",
        "content_url": "http://www.dianping.com/shop/66973494",
        "created_at": "2017-06-07 15:18:56",
        "updated_at": "2019-07-29 15:59:08",
        "audio_url": null,
        "status": "normal",
        "picture_url": "/newspics/1564050302.png",
        "content": "这是什么",
        "newslog": "read"
    }
]
```

#### 点击通知消息内容url记录消息已读:

- 描述：`POST`   [ /news/newlog](http://app-dev.mikwine.com/api/news/newlog?user_id=1&new_id=21)

| 参数        | 数据类型   | 是否必传 | 含义     | 备注        |
| --------    | ---------- | -------- | -------- | ----------- |
| user_id     | int        | 是       | 用户id   |             |
| user_id     | int        | 是       | 用户id   |             |
```json
 'code' => 200, 'message' => '生成log已读'
 'code' => 200, 'message' => 'log已经存在'
```