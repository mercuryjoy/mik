## 项目概述

* 产品名称：米客后台
* 项目代号：mik_admin
* 官方地址：http://app-dev.mikwine.com

米客后台 是一个后台管理应用，使用 Laravel5.2 编写而成。

## 运行环境要求

- Apache 2.4+
- PHP 5.5.9+
- Mysql 5.6+
- PHP扩展：OpenSSL
- PHP扩展：PDO
- PHP扩展：Mbstring
- PHP扩展：Tokenizer

### 基础安装

#### 1. 克隆源代码

克隆 `mik_admin` 源代码到本地：

    > svn checkout https://121.199.27.75:8088/svn/mik/B/PHP/mik-master mik

#### 3. 安装扩展包依赖

	composer install

#### 4. 生成配置文件

```
cp .env.example .env
```

你可以根据情况修改 `.env` 文件里的内容，如数据库连接、缓存、邮件设置等：

```
DB_HOST=127.0.0.1
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

#### 5. 生成数据表

在 mik 的根目录下运行以下命令

```shell
$ php artisan migrate
```

#### 7. 生成秘钥

```shell
php artisan key:generate
```

### 链接入口

* 管理后台：http://app-dev.mikwine.com

至此, 安装完成 ^_^。