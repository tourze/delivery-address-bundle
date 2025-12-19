<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\UpdateDeliveryAddressParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(UpdateDeliveryAddressParam::class)]
final class UpdateDeliveryAddressParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new UpdateDeliveryAddressParam(
            addressId: 123,
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame(123, $param->addressId);
        $this->assertNull($param->consignee);
        $this->assertNull($param->mobile);
    }

    public function testParamWithPartialUpdate(): void
    {
        $param = new UpdateDeliveryAddressParam(
            addressId: 456,
            consignee: '王五',
            mobile: '13700137000',
            setDefault: true,
        );

        $this->assertSame(456, $param->addressId);
        $this->assertSame('王五', $param->consignee);
        $this->assertSame('13700137000', $param->mobile);
        $this->assertTrue($param->setDefault);
        $this->assertNull($param->province);
    }
}
