<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\GetDeliveryAddressDetailParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetDeliveryAddressDetailParam::class)]
final class GetDeliveryAddressDetailParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetDeliveryAddressDetailParam(
            addressId: 101,
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame(101, $param->addressId);
    }
}
