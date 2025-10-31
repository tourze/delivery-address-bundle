# Delivery Address Bundle

[English](README.md) | [ä¸­æ–‡](README.zh-CN.md)

A Symfony bundle for managing delivery addresses with JSON-RPC Procedures and EasyAdmin integration.

## Features

- =æ Complete delivery address entity management
- =€ JSON-RPC procedures for address operations
- = User-based access control
- < Default address handling
- =Ê EasyAdmin CRUD interface
- >ê Comprehensive test coverage (162 tests)
- =Ë Multi-language support (EN/CN)

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

## JSON-RPC Methods

### Address Management

- `delivery_address.list` - Get user's address list
- `delivery_address.detail` - Get address details by ID
- `delivery_address.create` - Create new delivery address
- `delivery_address.update` - Update existing address
- `delivery_address.delete` - Delete address by ID

### Default Address

- `delivery_address.set_default` - Set address as default
- `delivery_address.get_default` - Get user's default address

## Entity

### DeliveryAddress

The main entity representing a delivery address:

```php
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;

// Entity fields:
// - id: Primary key
// - userId: Owner user ID
// - consignee: Recipient name
// - mobile: Contact phone number
// - country: Country name
// - province: Province/state
// - city: City name
// - district: District/county
// - addressLine: Detailed address
// - postalCode: Postal/ZIP code
// - addressTag: Address label (home, office, etc.)
// - isDefault: Default address flag
// - createTime/updateTime: Timestamps
```

## Admin Interface

The bundle provides an EasyAdmin CRUD controller at `/admin` for address management with:

- List view with filtering and searching
- Form creation and editing
- Batch operations
- User-based data isolation

## Development

### Running Tests

```bash
./vendor/bin/phpunit packages/delivery-address-bundle/tests
```

### Code Quality

```bash
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/delivery-address-bundle
```

## License

MIT License. See [LICENSE](LICENSE) file for details.

## Requirements

- PHP 8.1+
- Symfony 7.3+
- Doctrine ORM 3.0+

## Dependencies

This bundle requires several other packages from the `tourze/*` ecosystem. See [composer.json](composer.json) for the complete list.