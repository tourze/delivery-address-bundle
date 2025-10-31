<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\DeliveryAddressBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressBundle::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryAddressBundleTest extends AbstractBundleTestCase
{
}
