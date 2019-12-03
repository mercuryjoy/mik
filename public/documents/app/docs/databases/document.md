# 活动表activities
+-------------+-------------------------------------------+------+-----+---------+----------------+
| Field       | Type                                      | Null | Key | Default | Extra          |
+-------------+-------------------------------------------+------+-----+---------+----------------+
| id          | int(10) unsigned                          | NO   | PRI | NULL    | auto_increment |
| title       | varchar(30)                               | NO   |     | NULL    |                |
| rule_json   | varchar(1024)                             | NO   |     | NULL    |                |
| start_at    | datetime                                  | YES  |     | NULL    |                |
| end_at      | datetime                                  | YES  |     | NULL    |                |
| type        | enum('red_envelope','point','shop_owner') | NO   |     | NULL    |                |
| action_zone | enum('all','part')                        | NO   |     | NULL    |                |
| status      | enum('stop','normal')                     | NO   |     | stop    |                |
| created_at  | timestamp                                 | YES  |     | NULL    |                |
| updated_at  | timestamp                                 | YES  |     | NULL    |                |
| deleted_at  | timestamp                                 | YES  |     | NULL    |                |
+-------------+-------------------------------------------+------+-----+---------+----------------+

# 管理员表admins
+----------------+------------------+------+-----+---------+----------------+
| Field          | Type             | Null | Key | Default | Extra          |
+----------------+------------------+------+-----+---------+----------------+
| id             | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name           | varchar(255)     | NO   |     | NULL    |                |
| email          | varchar(255)     | NO   | UNI | NULL    |                |
| password       | varchar(60)      | NO   |     | NULL    |                |
| level          | int(11)          | NO   |     | 1       |                |
| remember_token | varchar(100)     | YES  |     | NULL    |                |
| deleted_at     | timestamp        | YES  |     | NULL    |                |
| created_at     | timestamp        | YES  |     | NULL    |                |
| updated_at     | timestamp        | YES  |     | NULL    |                |
+----------------+------------------+------+-----+---------+----------------+

# APP版本表app_versions
+-----------------+-------------------------------+------+-----+---------+----------------+
| Field           | Type                          | Null | Key | Default | Extra          |
+-----------------+-------------------------------+------+-----+---------+----------------+
| id              | int(10) unsigned              | NO   | PRI | NULL    | auto_increment |
| version         | varchar(20)                   | NO   |     | NULL    |                |
| type            | enum('android','ios','other') | NO   |     | NULL    |                |
| description     | text                          | NO   |     | NULL    |                |
| download_url    | varchar(255)                  | NO   |     | NULL    |                |
| version_code    | varchar(255)                  | NO   |     | NULL    |                |
| is_force_update | enum('yes','no')              | NO   |     | NULL    |                |
| created_at      | timestamp                     | YES  |     | NULL    |                |
| updated_at      | timestamp                     | YES  |     | NULL    |                |
| deleted_at      | timestamp                     | YES  |     | NULL    |                |
+-----------------+-------------------------------+------+-----+---------+----------------+

# 地区表areas
+----------------+-------------+------+-----+---------+-------+
| Field          | Type        | Null | Key | Default | Extra |
+----------------+-------------+------+-----+---------+-------+
| id             | int(11)     | NO   | PRI | NULL    |       |
| name           | varchar(20) | NO   |     | NULL    |       |
| parent_id      | int(11)     | YES  |     | NULL    |       |
| grandparent_id | int(11)     | YES  |     | NULL    |       |
| display        | varchar(30) | NO   |     | NULL    |       |
| deleted_at     | timestamp   | YES  |     | NULL    |       |
| created_at     | timestamp   | YES  |     | NULL    |       |
| updated_at     | timestamp   | YES  |     | NULL    |       |
+----------------+-------------+------+-----+---------+-------+

# 餐饮类别表categories
+------------+------------------+------+-----+---------+----------------+
| Field      | Type             | Null | Key | Default | Extra          |
+------------+------------------+------+-----+---------+----------------+
| id         | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| parent_id  | int(11)          | NO   |     | NULL    |                |
| name       | varchar(20)      | NO   |     | NULL    |                |
| level      | tinyint(4)       | NO   |     | 0       |                |
| created_at | timestamp        | YES  |     | NULL    |                |
| updated_at | timestamp        | YES  |     | NULL    |                |
| deleted_at | timestamp        | YES  |     | NULL    |                |
+------------+------------------+------+-----+---------+----------------+

# 二维码批次表code_batches
+------------+---------------------------+------+-----+---------+----------------+
| Field      | Type                      | Null | Key | Default | Extra          |
+------------+---------------------------+------+-----+---------+----------------+
| id         | int(10) unsigned          | NO   | PRI | NULL    | auto_increment |
| name       | varchar(100)              | NO   | UNI | NULL    |                |
| count      | int(11)                   | NO   |     | NULL    |                |
| status     | enum('normal','frozen')   | NO   |     | NULL    |                |
| deleted_at | timestamp                 | YES  |     | NULL    |                |
| created_at | timestamp                 | YES  |     | NULL    |                |
| updated_at | timestamp                 | YES  |     | NULL    |                |
| type       | enum('activity','normal') | NO   |     | normal  |                |
+------------+---------------------------+------+-----+---------+----------------+

# 二维码表codes
+------------------+------------------+------+-----+---------+----------------+
| Field            | Type             | Null | Key | Default | Extra          |
+------------------+------------------+------+-----+---------+----------------+
| id               | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| code             | varchar(20)      | NO   | UNI | NULL    |                |
| batch_id         | int(11)          | NO   |     | NULL    |                |
| scan_log_id      | int(11)          | NO   |     | NULL    |                |
| deleted_at       | timestamp        | YES  |     | NULL    |                |
| created_at       | timestamp        | YES  |     | NULL    |                |
| updated_at       | timestamp        | YES  |     | NULL    |                |
| user_scan_log_id | int(11)          | NO   |     | NULL    |                |
+------------------+------------------+------+-----+---------+----------------+

# 经销商表distributors
+-----------------------+------------------+------+-----+---------+----------------+
| Field                 | Type             | Null | Key | Default | Extra          |
+-----------------------+------------------+------+-----+---------+----------------+
| id                    | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name                  | varchar(255)     | NO   |     | NULL    |                |
| level                 | int(11)          | NO   |     | 1       |                |
| parent_distributor_id | int(11)          | YES  |     | NULL    |                |
| area_id               | int(11)          | NO   |     | NULL    |                |
| address               | varchar(200)     | NO   |     |         |                |
| contact               | varchar(20)      | NO   |     |         |                |
| telephone             | varchar(20)      | NO   |     |         |                |
| deleted_at            | timestamp        | YES  |     | NULL    |                |
| created_at            | timestamp        | YES  |     | NULL    |                |
| updated_at            | timestamp        | YES  |     | NULL    |                |
+-----------------------+------------------+------+-----+---------+----------------+

# 扫码规则表draw_rules
+----------------+------------------+------+-----+---------+----------------+
| Field          | Type             | Null | Key | Default | Extra          |
+----------------+------------------+------+-----+---------+----------------+
| id             | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| area_id        | int(11)          | YES  | UNI | NULL    |                |
| distributor_id | int(11)          | YES  | UNI | NULL    |                |
| shop_id        | int(11)          | YES  | UNI | NULL    |                |
| rule_json      | varchar(1024)    | NO   |     | NULL    |                |
| created_at     | timestamp        | YES  |     | NULL    |                |
| updated_at     | timestamp        | YES  |     | NULL    |                |
+----------------+------------------+------+-----+---------+----------------+

# 反馈表feedback
+------------+------------------+------+-----+---------+----------------+
| Field      | Type             | Null | Key | Default | Extra          |
+------------+------------------+------+-----+---------+----------------+
| id         | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| user_id    | int(11)          | YES  |     | NULL    |                |
| content    | text             | NO   |     | NULL    |                |
| deleted_at | timestamp        | YES  |     | NULL    |                |
| created_at | timestamp        | YES  |     | NULL    |                |
| updated_at | timestamp        | YES  |     | NULL    |                |
+------------+------------------+------+-----+---------+----------------+

# 资金池记录表funding_pool_logs
+------------+---------------------------------+------+-----+---------+----------------+
| Field      | Type                            | Null | Key | Default | Extra          |
+------------+---------------------------------+------+-----+---------+----------------+
| id         | int(10) unsigned                | NO   | PRI | NULL    | auto_increment |
| type       | enum('deposit','user_withdraw') | NO   |     | NULL    |                |
| amount     | int(11)                         | NO   |     | NULL    |                |
| balance    | int(11)                         | NO   |     | NULL    |                |
| user_id    | int(11)                         | YES  |     | NULL    |                |
| admin_id   | int(11)                         | YES  |     | NULL    |                |
| comment    | text                            | NO   |     | NULL    |                |
| deleted_at | timestamp                       | YES  |     | NULL    |                |
| created_at | timestamp                       | YES  |     | NULL    |                |
| updated_at | timestamp                       | YES  |     | NULL    |                |
+------------+---------------------------------+------+-----+---------+----------------+

# 数据表版本迁移表migrations
+-----------+--------------+------+-----+---------+-------+
| Field     | Type         | Null | Key | Default | Extra |
+-----------+--------------+------+-----+---------+-------+
| migration | varchar(255) | NO   |     | NULL    |       |
| batch     | int(11)      | NO   |     | NULL    |       |
+-----------+--------------+------+-----+---------+-------+

# Net接口访问记录表net_api_logs
+---------------+--------------------------+------+-----+---------+----------------+
| Field         | Type                     | Null | Key | Default | Extra          |
+---------------+--------------------------+------+-----+---------+----------------+
| id            | int(10) unsigned         | NO   | PRI | NULL    | auto_increment |
| user_id       | int(11)                  | NO   |     | NULL    |                |
| shop_id       | int(11)                  | NO   |     | NULL    |                |
| code_id       | int(11)                  | NO   |     | NULL    |                |
| api_id        | int(11)                  | NO   |     | NULL    |                |
| net_user_id   | int(11)                  | NO   |     | NULL    |                |
| net_user_name | varchar(20)              | NO   |     | NULL    |                |
| role          | enum('user','net_user')  | NO   |     | NULL    |                |
| status        | enum('success','failed') | NO   |     | NULL    |                |
| comment       | varchar(255)             | NO   |     | NULL    |                |
| created_at    | timestamp                | YES  |     | NULL    |                |
| updated_at    | timestamp                | YES  |     | NULL    |                |
| deleted_at    | timestamp                | YES  |     | NULL    |                |
+---------------+--------------------------+------+-----+---------+----------------+

# 新闻表news
+---------------+------------------+------+-----+---------+----------------+
| Field         | Type             | Null | Key | Default | Extra          |
+---------------+------------------+------+-----+---------+----------------+
| id            | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| title         | varchar(50)      | NO   |     | NULL    |                |
| thumbnail_url | varchar(1024)    | NO   |     | NULL    |                |
| content_url   | varchar(1024)    | NO   |     | NULL    |                |
| created_at    | timestamp        | YES  |     | NULL    |                |
| updated_at    | timestamp        | YES  |     | NULL    |                |
+---------------+------------------+------+-----+---------+----------------+

# 取消订单审核表order_drawbacks
+----------------+------------------------------------------+------+-----+---------+----------------+
| Field          | Type                                     | Null | Key | Default | Extra          |
+----------------+------------------------------------------+------+-----+---------+----------------+
| id             | int(10) unsigned                         | NO   | PRI | NULL    | auto_increment |
| user_id        | int(11)                                  | NO   |     | NULL    |                |
| store_order_id | int(11)                                  | NO   |     | NULL    |                |
| pay_money      | int(11)                                  | NO   |     | NULL    |                |
| drawback_money | int(11)                                  | NO   |     | NULL    |                |
| pay_way        | enum('balance','alipay','wechat','line') | YES  |     | NULL    |                |
| status         | enum('check','finished')                 | NO   |     | check   |                |
| source         | enum('cancel')                           | NO   |     | NULL    |                |
| created_at     | timestamp                                | YES  |     | NULL    |                |
| updated_at     | timestamp                                | YES  |     | NULL    |                |
| deleted_at     | timestamp                                | YES  |     | NULL    |                |
+----------------+------------------------------------------+------+-----+---------+----------------+

# 支付记录表pay_logs
+------------------+------------------+------+-----+---------+----------------+
| Field            | Type             | Null | Key | Default | Extra          |
+------------------+------------------+------+-----+---------+----------------+
| id               | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| order_id         | int(11)          | NO   |     | NULL    |                |
| seller_id        | varchar(50)      | NO   |     | NULL    |                |
| seller_email     | varchar(50)      | NO   |     | NULL    |                |
| buyer_id         | varchar(50)      | NO   |     | NULL    |                |
| buyer_logon_id   | varchar(50)      | NO   |     | NULL    |                |
| buyer_pay_amount | int(11)          | NO   |     | NULL    |                |
| gmt_payment      | datetime         | NO   |     | NULL    |                |
| notify_time      | datetime         | NO   |     | NULL    |                |
| trade_no         | varchar(50)      | NO   |     | NULL    |                |
| trade_status     | varchar(20)      | NO   |     | NULL    |                |
| pay_way          | varchar(20)      | NO   |     | NULL    |                |
| created_at       | timestamp        | YES  |     | NULL    |                |
| updated_at       | timestamp        | YES  |     | NULL    |                |
+------------------+------------------+------+-----+---------+----------------+

# 支付方式表pays
+-------------+------------------------------------------+------+-----+---------+----------------+
| Field       | Type                                     | Null | Key | Default | Extra          |
+-------------+------------------------------------------+------+-----+---------+----------------+
| id          | int(10) unsigned                         | NO   | PRI | NULL    | auto_increment |
| pay_way     | enum('balance','alipay','wechat','line') | NO   |     | NULL    |                |
| status      | tinyint(4)                               | NO   |     | 0       |                |
| is_default  | tinyint(1)                               | NO   |     | NULL    |                |
| description | varchar(255)                             | YES  |     | NULL    |                |
| created_at  | timestamp                                | YES  |     | NULL    |                |
| updated_at  | timestamp                                | YES  |     | NULL    |                |
| deleted_at  | timestamp                                | YES  |     | NULL    |                |
+-------------+------------------------------------------+------+-----+---------+----------------+

# 字段数据修改记录表revisions
+-------------------+------------------+------+-----+---------+----------------+
| Field             | Type             | Null | Key | Default | Extra          |
+-------------------+------------------+------+-----+---------+----------------+
| id                | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| revisionable_type | varchar(255)     | NO   |     | NULL    |                |
| revisionable_id   | int(11)          | NO   | MUL | NULL    |                |
| admin_id          | int(11)          | YES  |     | NULL    |                |
| key               | varchar(255)     | NO   |     | NULL    |                |
| old_value         | text             | YES  |     | NULL    |                |
| new_value         | text             | YES  |     | NULL    |                |
| created_at        | timestamp        | YES  |     | NULL    |                |
| updated_at        | timestamp        | YES  |     | NULL    |                |
+-------------------+------------------+------+-----+---------+----------------+

# 销售员表salesmen
+------------+------------------+------+-----+---------+----------------+
| Field      | Type             | Null | Key | Default | Extra          |
+------------+------------------+------+-----+---------+----------------+
| id         | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| name       | varchar(255)     | NO   |     | NULL    |                |
| phone      | varchar(255)     | NO   |     | NULL    |                |
| deleted_at | timestamp        | YES  |     | NULL    |                |
| created_at | timestamp        | YES  |     | NULL    |                |
| updated_at | timestamp        | YES  |     | NULL    |                |
+------------+------------------+------+-----+---------+----------------+

# 扫码记录表scan_logs
+----------------+-------------------------------------------------------------+------+-----+---------+----------------+
| Field          | Type                                                        | Null | Key | Default | Extra          |
+----------------+-------------------------------------------------------------+------+-----+---------+----------------+
| id             | int(10) unsigned                                            | NO   | PRI | NULL    | auto_increment |
| code_id        | int(11)                                                     | NO   |     | NULL    |                |
| user_id        | int(11)                                                     | NO   |     | NULL    |                |
| shop_id        | int(11)                                                     | YES  |     | NULL    |                |
| luck_id        | varchar(10)                                                 | NO   |     | NULL    |                |
| money          | int(11)                                                     | NO   |     | NULL    |                |
| point          | int(11)                                                     | NO   |     | NULL    |                |
| deleted_at     | timestamp                                                   | YES  |     | NULL    |                |
| created_at     | timestamp                                                   | YES  |     | NULL    |                |
| updated_at     | timestamp                                                   | YES  |     | NULL    |                |
| type           | enum('scan_prize','scan_coupon','scan_send_money_activity') | NO   |     | NULL    |                |
| waiter_id      | int(11)                                                     | NO   |     | NULL    |                |
| net_user_id    | int(11)                                                     | NO   |     | NULL    |                |
| net_user_name  | varchar(20)                                                 | NO   |     | NULL    |                |
| net_user_times | int(11)                                                     | NO   |     | NULL    |                |
| coupon_name    | varchar(30)                                                 | NO   |     | NULL    |                |
| coupon_code    | varchar(20)                                                 | NO   |     | NULL    |                |
+----------------+-------------------------------------------------------------+------+-----+---------+----------------+

# 全局配置表settings
+---------------+--------------+------+-----+---------+-------+
| Field         | Type         | Null | Key | Default | Extra |
+---------------+--------------+------+-----+---------+-------+
| setting_key   | varchar(100) | NO   | PRI | NULL    |       |
| setting_value | text         | YES  |     | NULL    |       |
+---------------+--------------+------+-----+---------+-------+

# 终端和活动关系表shop_activity
+-------------+------------------+------+-----+---------+----------------+
| Field       | Type             | Null | Key | Default | Extra          |
+-------------+------------------+------+-----+---------+----------------+
| id          | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| shop_id     | int(11)          | NO   |     | NULL    |                |
| activity_id | int(11)          | NO   |     | NULL    |                |
+-------------+------------------+------+-----+---------+----------------+

# 终端表shops
+----------------+-----------------------+------+-----+---------+----------------+
| Field          | Type                  | Null | Key | Default | Extra          |
+----------------+-----------------------+------+-----+---------+----------------+
| id             | int(10) unsigned      | NO   | PRI | NULL    | auto_increment |
| name           | varchar(255)          | NO   |     | NULL    |                |
| level          | enum('A','B','C','D') | NO   |     | NULL    |                |
| distributor_id | int(11)               | YES  |     | NULL    |                |
| area_id        | int(11)               | NO   |     | NULL    |                |
| address        | varchar(200)          | NO   |     |         |                |
| salesman_id    | int(11)               | NO   |     | NULL    |                |
| deleted_at     | timestamp             | YES  |     | NULL    |                |
| created_at     | timestamp             | YES  |     | NULL    |                |
| updated_at     | timestamp             | YES  |     | NULL    |                |
| logo           | varchar(255)          | NO   |     | NULL    |                |
| owner_id       | int(11)               | NO   |     | NULL    |                |
| contact_person | varchar(20)           | NO   |     | NULL    |                |
| contact_phone  | varchar(13)           | NO   |     | NULL    |                |
| category_id    | int(11)               | NO   |     | NULL    |                |
| per_consume    | decimal(5,2)          | NO   |     | NULL    |                |
| source         | tinyint(4)            | NO   |     | NULL    |                |
| net_shop_id    | int(11)               | YES  | UNI | NULL    |                |
+----------------+-----------------------+------+-----+---------+----------------+

# 短信记录表sms_logs
+------------+--------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+
| Field      | Type                                                                                                                                 | Null | Key | Default | Extra          |
+------------+--------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+
| id         | int(10) unsigned                                                                                                                     | NO   | PRI | NULL    | auto_increment |
| telephone  | varchar(20)                                                                                                                          | NO   | MUL | NULL    |                |
| content    | varchar(100)                                                                                                                         | NO   |     | NULL    |                |
| type       | enum('verify_register','pass_audit','admin_notify_daily_cost','admin_notify_scan_count','admin_notify_funding_pool','test','others') | NO   |     | NULL    |                |
| status     | enum('sent','error','used')                                                                                                          | NO   |     | NULL    |                |
| code       | varchar(10)                                                                                                                          | YES  |     | NULL    |                |
| comment    | varchar(100)                                                                                                                         | YES  |     | NULL    |                |
| created_at | timestamp                                                                                                                            | YES  |     | NULL    |                |
| updated_at | timestamp                                                                                                                            | YES  |     | NULL    |                |
+------------+--------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+

# 商品表store_items
+-------------+-------------------------------------------+------+-----+----------+----------------+
| Field       | Type                                      | Null | Key | Default  | Extra          |
+-------------+-------------------------------------------+------+-----+----------+----------------+
| id          | int(10) unsigned                          | NO   | PRI | NULL     | auto_increment |
| name        | varchar(50)                               | NO   |     | NULL     |                |
| description | varchar(1024)                             | NO   |     | NULL     |                |
| price_money | int(11)                                   | NO   |     | 0        |                |
| price_point | int(11)                                   | NO   |     | 0        |                |
| is_virtual  | tinyint(1)                                | NO   |     | NULL     |                |
| stock       | int(11)                                   | NO   |     | 0        |                |
| photo_url   | varchar(1024)                             | NO   |     | NULL     |                |
| status      | enum('in_stock','out_of_stock','deleted') | NO   |     | in_stock |                |
| deleted_at  | timestamp                                 | YES  |     | NULL     |                |
| created_at  | timestamp                                 | YES  |     | NULL     |                |
| updated_at  | timestamp                                 | YES  |     | NULL     |                |
| type        | enum('exchange','purchase')               | NO   |     | NULL     |                |
+-------------+-------------------------------------------+------+-----+----------+----------------+

# 订单表store_orders
+------------------+--------------------------------------------------------------------------+------+-----+---------+----------------+
| Field            | Type                                                                     | Null | Key | Default | Extra          |
+------------------+--------------------------------------------------------------------------+------+-----+---------+----------------+
| id               | int(10) unsigned                                                         | NO   | PRI | NULL    | auto_increment |
| user_id          | int(11)                                                                  | NO   |     | NULL    |                |
| item_id          | int(11)                                                                  | NO   |     | NULL    |                |
| amount           | int(11)                                                                  | NO   |     | NULL    |                |
| shipping_address | varchar(1000)                                                            | YES  |     | NULL    |                |
| status           | enum('created','established','shipped','canceled','drawback','finished') | YES  |     | NULL    |                |
| remarks          | varchar(1000)                                                            | YES  |     |         |                |
| deleted_at       | timestamp                                                                | YES  |     | NULL    |                |
| created_at       | timestamp                                                                | YES  |     | NULL    |                |
| updated_at       | timestamp                                                                | YES  |     | NULL    |                |
| type             | enum('exchange','purchase')                                              | NO   |     | NULL    |                |
| contact_name     | varchar(30)                                                              | YES  |     | NULL    |                |
| contact_phone    | varchar(30)                                                              | YES  |     | NULL    |                |
| money            | int(11)                                                                  | NO   |     | NULL    |                |
| is_pay           | tinyint(1)                                                               | NO   |     | 0       |                |
| pay_way          | enum('balance','alipay','wechat','line')                                 | YES  |     | NULL    |                |
| is_checked       | tinyint(1)                                                               | NO   |     | 0       |                |
| salesman_id      | int(11)                                                                  | YES  |     | NULL    |                |
| distributor_id   | int(11)                                                                  | YES  |     | NULL    |                |
| shop_id          | int(11)                                                                  | YES  |     | NULL    |                |
+------------------+--------------------------------------------------------------------------+------+-----+---------+----------------+

# 用户金额记录表user_money_logs
+-------------+----------------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+
| Field       | Type                                                                                                                                         | Null | Key | Default | Extra          |
+-------------+----------------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+
| id          | int(10) unsigned                                                                                                                             | NO   | PRI | NULL    | auto_increment |
| type        | enum('adjustment','scan_prize','red_envelope','withdraw','exchange_to_point','store_order_use','user_scan_to_waiter','waiter_scan_to_owner') | YES  |     | NULL    |                |
| amount      | int(11)                                                                                                                                      | NO   |     | NULL    |                |
| user_id     | int(11)                                                                                                                                      | NO   |     | NULL    |                |
| admin_id    | int(11)                                                                                                                                      | YES  |     | NULL    |                |
| scan_log_id | int(11)                                                                                                                                      | YES  |     | NULL    |                |
| comment     | text                                                                                                                                         | NO   |     | NULL    |                |
| created_at  | timestamp                                                                                                                                    | YES  |     | NULL    |                |
| updated_at  | timestamp                                                                                                                                    | YES  |     | NULL    |                |
+-------------+----------------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+

# 用户积分记录表user_point_logs
+----------------+-------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+
| Field          | Type                                                                                                                                | Null | Key | Default | Extra          |
+----------------+-------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+
| id             | int(10) unsigned                                                                                                                    | NO   | PRI | NULL    | auto_increment |
| type           | enum('adjustment','scan_prize','red_envelope','store_order_use','exchange_from_money','user_scan_to_waiter','waiter_scan_to_owner') | YES  |     | NULL    |                |
| amount         | int(11)                                                                                                                             | NO   |     | NULL    |                |
| user_id        | int(11)                                                                                                                             | NO   |     | NULL    |                |
| admin_id       | int(11)                                                                                                                             | YES  |     | NULL    |                |
| scan_log_id    | int(11)                                                                                                                             | YES  |     | NULL    |                |
| store_order_id | int(11)                                                                                                                             | YES  |     | NULL    |                |
| comment        | text                                                                                                                                | NO   |     | NULL    |                |
| created_at     | timestamp                                                                                                                           | YES  |     | NULL    |                |
| updated_at     | timestamp                                                                                                                           | YES  |     | NULL    |                |
+----------------+-------------------------------------------------------------------------------------------------------------------------------------+------+-----+---------+----------------+

# 服务员表users
+---------------+--------------------------+------+-----+---------+----------------+
| Field         | Type                     | Null | Key | Default | Extra          |
+---------------+--------------------------+------+-----+---------+----------------+
| id            | int(10) unsigned         | NO   | PRI | NULL    | auto_increment |
| name          | varchar(20)              | NO   |     | NULL    |                |
| gender        | enum('male','female')    | YES  |     | NULL    |                |
| telephone     | varchar(20)              | NO   | UNI | NULL    |                |
| password      | varchar(60)              | YES  |     | NULL    |                |
| shop_id       | int(11)                  | YES  |     | NULL    |                |
| status        | enum('pending','normal') | NO   |     | pending |                |
| money_balance | int(11)                  | NO   |     | 0       |                |
| point_balance | int(11)                  | NO   |     | 0       |                |
| wechat_openid | varchar(100)             | YES  |     | NULL    |                |
| deleted_at    | timestamp                | YES  |     | NULL    |                |
| created_at    | timestamp                | YES  |     | NULL    |                |
| updated_at    | timestamp                | YES  |     | NULL    |                |
| active        | tinyint(1)               | NO   |     | NULL    |                |
+---------------+--------------------------+------+-----+---------+----------------+

