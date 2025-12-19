<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\DeleteDeliveryAddressParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(DeleteDeliveryAddressParam::class)]
final class DeleteDeliveryAddressParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new DeleteDeliveryAddressParam(
            addressId: 789,
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame(789, $param->addressId);
    }
}
