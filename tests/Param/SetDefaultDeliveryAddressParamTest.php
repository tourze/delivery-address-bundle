<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\SetDefaultDeliveryAddressParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(SetDefaultDeliveryAddressParam::class)]
final class SetDefaultDeliveryAddressParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new SetDefaultDeliveryAddressParam(
            addressId: 202,
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame(202, $param->addressId);
    }
}
