<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\GetDefaultDeliveryAddressParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetDefaultDeliveryAddressParam::class)]
final class GetDefaultDeliveryAddressParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetDefaultDeliveryAddressParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetDefaultDeliveryAddressParam();

        $this->assertInstanceOf(GetDefaultDeliveryAddressParam::class, $param);
    }
}
