# 收货地址管理 Bundle

[English](README.md) | [中文](README.zh-CN.md)

一个用于管理收货地址的 Symfony Bundle，提供 JSON-RPC Procedures 和 EasyAdmin 后台管理集成。

## 功能特性

- ✅ 完整的收货地址实体管理
- 🔌 JSON-RPC 接口支持地址操作
- 🔐 基于用户的访问控制
- ⭐ 默认地址处理
- 🎨 EasyAdmin CRUD 后台界面
- 🧪 全面的测试覆盖 (238 个测试)
- 🌐 多语言支持 (EN/CN)
- 📍 支持 GB/T 2261 行政区划代码

## 安装

```bash
composer require tourze/delivery-address-bundle
```

## 配置

在 `config/bundles.php` 中添加 Bundle:

```php
return [
    // ...
    Tourze\DeliveryAddressBundle\DeliveryAddressBundle::class => ['all' => true],
];
```

运行数据库迁移创建表结构:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## JSON-RPC 接口方法

### 地址管理

- `GetDeliveryAddressList` - 获取用户地址列表(带分页)
- `GetDeliveryAddressDetail` - 根据ID获取地址详情
- `CreateDeliveryAddress` - 创建新的收货地址
- `UpdateDeliveryAddress` - 更新现有地址
- `DeleteDeliveryAddress` - 根据ID删除地址

### 默认地址

- `SetDefaultDeliveryAddress` - 设置默认地址
- `GetDefaultDeliveryAddress` - 获取用户的默认地址

> **注意**: 所有接口方法都需要用户认证 (`IS_AUTHENTICATED_FULLY`)

## 实体

### DeliveryAddress

表示收货地址的主实体:

```php
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;

// 实体字段:
// - id: 主键 (BIGINT)
// - user: UserInterface 关联 (所有者)
// - sn: 唯一雪花ID标识
// - consignee: 收货人姓名 (最大50字符)
// - mobile: 联系电话 (最大20字符)
// - country: 国家名称 (可选, 最大64字符)
// - province: 省/州 (最大64字符)
// - provinceCode: 省代码 (可选, 最大20字符)
// - city: 城市名称 (最大64字符)
// - cityCode: 城市代码 (可选, 最大20字符)
// - district: 区/县 (最大64字符)
// - districtCode: 区/县代码 (可选, 最大20字符)
// - addressLine: 详细地址 (最大255字符)
// - postalCode: 邮政编码 (可选, 最大20字符)
// - addressTag: 地址标签如"家"、"公司"等 (可选, 最大20字符)
// - gender: 性别枚举 (可选, 使用 GB/T 2261 标准)
// - isDefault: 是否默认地址 (布尔值)
// - createTime/updateTime: 时间戳 (自动管理)
// - createdBy/updatedBy: 操作人追踪
// - createdFromIp/updatedFromIp: IP地址追踪
```

## Repository 仓储

`DeliveryAddressRepository` 提供了多个查询方法:

```php
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;

// 通过用户对象查询
$qb = $repository->buildListQueryByUser($user);

// 通过用户标识符查询 (使用 UserInterface::getUserIdentifier())
$qb = $repository->buildListQueryByUserId('user123');

// 查找默认地址
$address = $repository->findDefaultByUser($user);
$address = $repository->findDefaultByUserId('user123');

// 取消用户的默认地址
$affectedRows = $repository->unsetDefaultForUser($user);
```

> **重要提示**: `buildListQueryByUserId()` 和 `findDefaultByUserId()` 方法使用 `userIdentifier` 字段 (Symfony 6+ 标准)，而不是 `username`。

## 后台管理界面

Bundle 提供了 EasyAdmin CRUD 控制器 (访问路径 `/admin`)，包含以下功能:

- 列表视图支持过滤和搜索
- 表单创建和编辑
- 批量操作
- 基于用户的数据隔离
- 自动 IP 和用户追踪

## 开发

### 运行测试

```bash
# 运行所有测试 (238个)
./vendor/bin/phpunit packages/delivery-address-bundle/tests

# 运行特定测试套件
./vendor/bin/phpunit packages/delivery-address-bundle/tests/Entity
./vendor/bin/phpunit packages/delivery-address-bundle/tests/Repository
./vendor/bin/phpunit packages/delivery-address-bundle/tests/Procedure
```

### 代码质量检查

```bash
# PHPStan 静态分析 (Level 9)
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/delivery-address-bundle/src --level=9
```

## 架构设计

### 服务层

- `DeliveryAddressService`: 地址操作的业务逻辑层
- 处理验证、默认地址管理和实体生命周期

### 事件监听器

- `DeliveryAddressEntityListener`: Doctrine 实体生命周期事件
- 自动字段验证和一致性检查

### 数据填充

- `DeliveryAddressFixtures`: 用于开发和测试的测试数据生成
- 使用 `UserManagerInterface` 创建用户

## 系统要求

- PHP 8.1+
- Symfony 7.3+
- Doctrine ORM 3.0+

## 依赖包

本 Bundle 依赖 `tourze/*` 生态系统中的多个包:
- `tourze/json-rpc-core`: JSON-RPC 服务器基础设施
- `tourze/doctrine-*-bundle`: Doctrine 扩展 (时间戳、IP追踪、用户追踪等)
- `tourze/easy-admin-*-bundle`: EasyAdmin 扩展
- `tourze/user-service-contracts`: 用户管理契约

完整列表请查看 [composer.json](composer.json)。

## 许可证

MIT License。详见 [LICENSE](LICENSE) 文件。
