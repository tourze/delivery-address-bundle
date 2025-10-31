<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\DeliveryAddressBundle\DependencyInjection\DeliveryAddressExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressExtension::class)]
final class DeliveryAddressExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
