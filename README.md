# webman casbin 权限控制插件

[![license](https://img.shields.io/github/license/Tinywan/casbin)]()
[![996.icu](https://img.shields.io/badge/link-996.icu-red.svg)](https://996.icu)
[![Build status](https://github.com/Tinywan/dnmp/workflows/CI/badge.svg)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/casbin?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/casbin/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/casbin?color=ff69b4)]()

webman casbin 权限控制插件。它基于 [PHP-Casbin](https://github.com/php-casbin/php-casbin), 一个强大的、高效的开源访问控制框架，支持基于`ACL`, `RBAC`, `ABAC`等访问控制模型。

在这之前，你需要了解 [Casbin](https://github.com/php-casbin/php-casbin) 的相关知识。

> 插件需要webman>=1.2.0 webman-framework>=1.2.0

## 依赖

- [ThinkORM](https://github.com/top-think/think-orm)
- [PHP-DI](https://github.com/PHP-DI/PHP-DI)

## 安装

```sh
composer require tinywan/casbin
```

## 配置

### 数据库配置

（1）创建数据库 `webman`

（2）创建数据表

```sql
CREATE TABLE `casbin_rule` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ptype` varchar(255) DEFAULT NULL,
  `v0` varchar(255) DEFAULT NULL,
  `v1` varchar(255) DEFAULT NULL,
  `v2` varchar(255) DEFAULT NULL,
  `v3` varchar(255) DEFAULT NULL,
  `v4` varchar(255) DEFAULT NULL,
  `v5` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_ptype` (`ptype`(191)) USING BTREE,
  KEY `idx_v0` (`v0`(191)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='策略规则表';
```

## 用法

### 快速开始

安装成功后，可以这样使用:

```php
use Tinywan\Casbin\Permission;

// adds permissions to a user
Permission::addPermissionForUser('eve', 'articles', 'read');
// adds a role for a user.
Permission::addRoleForUser('eve', 'writer');
// adds permissions to a rule
Permission::addPolicy('writer', 'articles','edit');
```

你可以检查一个用户是否拥有某个权限:

```php
if (Permission::enforce("eve", "articles", "edit")) {
    echo '恭喜你！通过权限认证';
} else {
    echo '对不起，您没有该资源访问权限';
}
```

更多 `API` 参考 [Casbin API](https://casbin.org/docs/en/management-api) 。

## 感谢

[Casbin](https://github.com/php-casbin/php-casbin)，你可以查看全部文档在其 [官网](https://casbin.org/) 上。
