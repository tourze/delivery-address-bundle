# Delivery Address Bundle

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for managing delivery addresses with JSON-RPC Procedures and EasyAdmin integration.

## Features

- ✅ Complete delivery address entity management
- 🔌 JSON-RPC procedures for address operations
- 🔐 User-based access control
- ⭐ Default address handling
- 🎨 EasyAdmin CRUD interface
- 🧪 Comprehensive test coverage (238 tests)
- 🌐 Multi-language support (EN/CN)
- 📍 GB/T 2261 administrative division codes support

## Installation

```bash
composer require tourze/delivery-address-bundle
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    Tourze\DeliveryAddressBundle\DeliveryAddressBundle::class => ['all' => true],
];
```

Run migrations to create the database tables:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## JSON-RPC Procedures

### Address Management

- `GetDeliveryAddressList` - Get user's address list with pagination
- `GetDeliveryAddressDetail` - Get address details by ID
- `CreateDeliveryAddress` - Create new delivery address
- `UpdateDeliveryAddress` - Update existing address
- `DeleteDeliveryAddress` - Delete address by ID

### Default Address

- `SetDefaultDeliveryAddress` - Set address as default
- `GetDefaultDeliveryAddress` - Get user's default address

> **Note**: All procedures require authentication (`IS_AUTHENTICATED_FULLY`)

## Entity

### DeliveryAddress

The main entity representing a delivery address:

```php
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;

// Entity fields:
// - id: Primary key (BIGINT)
// - user: UserInterface relation (owner)
// - sn: Unique snowflake identifier
// - consignee: Recipient name (max 50 chars)
// - mobile: Contact phone number (max 20 chars)
// - country: Country name (optional, max 64 chars)
// - province: Province/state (max 64 chars)
// - provinceCode: Province code (optional, max 20 chars)
// - city: City name (max 64 chars)
// - cityCode: City code (optional, max 20 chars)
// - district: District/county (max 64 chars)
// - districtCode: District code (optional, max 20 chars)
// - addressLine: Detailed address (max 255 chars)
// - postalCode: Postal/ZIP code (optional, max 20 chars)
// - addressTag: Address label like "home", "office" (optional, max 20 chars)
// - gender: Gender enum (optional, using GB/T 2261 standard)
// - isDefault: Default address flag (boolean)
// - createTime/updateTime: Timestamps (auto-managed)
// - createdBy/updatedBy: Blameable tracking
// - createdFromIp/updatedFromIp: IP tracking
```

## Repository

The `DeliveryAddressRepository` provides several query methods:

```php
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;

// Query by user object
$qb = $repository->buildListQueryByUser($user);

// Query by user identifier (uses UserInterface::getUserIdentifier())
$qb = $repository->buildListQueryByUserId('user123');

// Find default address
$address = $repository->findDefaultByUser($user);
$address = $repository->findDefaultByUserId('user123');

// Unset default for user
$affectedRows = $repository->unsetDefaultForUser($user);
```

> **Important**: The `buildListQueryByUserId()` and `findDefaultByUserId()` methods use `userIdentifier` field (Symfony 6+ standard), not `username`.

## Admin Interface

The bundle provides an EasyAdmin CRUD controller at `/admin` for address management with:

- List view with filtering and searching
- Form creation and editing
- Batch operations
- User-based data isolation
- Automatic IP and user tracking

## Development

### Running Tests

```bash
# Run all tests (238 tests)
./vendor/bin/phpunit packages/delivery-address-bundle/tests

# Run specific test suite
./vendor/bin/phpunit packages/delivery-address-bundle/tests/Entity
./vendor/bin/phpunit packages/delivery-address-bundle/tests/Repository
./vendor/bin/phpunit packages/delivery-address-bundle/tests/Procedure
```

### Code Quality

```bash
# PHPStan static analysis (Level 9)
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/delivery-address-bundle/src --level=9
```

## Architecture

### Service Layer

- `DeliveryAddressService`: Business logic layer for address operations
- Handles validation, default address management, and entity lifecycle

### Event Listeners

- `DeliveryAddressEntityListener`: Doctrine entity lifecycle events
- Automatic field validation and consistency checks

### Data Fixtures

- `DeliveryAddressFixtures`: Test data generation for development and testing
- Uses `UserManagerInterface` for user creation

## Requirements

- PHP 8.1+
- Symfony 7.3+
- Doctrine ORM 3.0+

## Dependencies

This bundle requires several other packages from the `tourze/*` ecosystem:
- `tourze/json-rpc-core`: JSON-RPC server infrastructure
- `tourze/doctrine-*-bundle`: Doctrine extensions (timestamps, IP tracking, user tracking, etc.)
- `tourze/easy-admin-*-bundle`: EasyAdmin extensions
- `tourze/user-service-contracts`: User management contracts

See [composer.json](composer.json) for the complete list.

## License

MIT License. See [LICENSE](LICENSE) file for details.
