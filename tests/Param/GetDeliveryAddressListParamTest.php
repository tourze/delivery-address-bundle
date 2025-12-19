<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\GetDeliveryAddressListParam;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

/**
 * @internal
 */
#[CoversClass(GetDeliveryAddressListParam::class)]
final class GetDeliveryAddressListParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetDeliveryAddressListParam(
            pageSize: 20,
            currentPage: 2,
            lastId: 100,
        );

        $this->assertInstanceOf(PaginatorParamInterface::class, $param);
        $this->assertSame(20, $param->pageSize);
        $this->assertSame(2, $param->currentPage);
        $this->assertSame(100, $param->lastId);
    }

    public function testParamWithDefaultValues(): void
    {
        $param = new GetDeliveryAddressListParam();

        $this->assertSame(10, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
        $this->assertNull($param->lastId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetDeliveryAddressListParam(
            pageSize: 50,
            currentPage: 3,
            lastId: 200,
        );

        $this->assertSame(50, $param->pageSize);
        $this->assertSame(3, $param->currentPage);
        $this->assertSame(200, $param->lastId);
    }

    public function testPageSizeAcceptsValidRange(): void
    {
        $param1 = new GetDeliveryAddressListParam(pageSize: 1);
        $this->assertSame(1, $param1->pageSize);

        $param2 = new GetDeliveryAddressListParam(pageSize: 2000);
        $this->assertSame(2000, $param2->pageSize);

        $param3 = new GetDeliveryAddressListParam(pageSize: 100);
        $this->assertSame(100, $param3->pageSize);
    }

    public function testCurrentPageAcceptsValidRange(): void
    {
        $param1 = new GetDeliveryAddressListParam(currentPage: 1);
        $this->assertSame(1, $param1->currentPage);

        $param2 = new GetDeliveryAddressListParam(currentPage: 1000);
        $this->assertSame(1000, $param2->currentPage);

        $param3 = new GetDeliveryAddressListParam(currentPage: 500);
        $this->assertSame(500, $param3->currentPage);
    }

    public function testLastIdCanBeNull(): void
    {
        $param = new GetDeliveryAddressListParam(lastId: null);
        $this->assertNull($param->lastId);
    }

    public function testLastIdCanBePositiveInteger(): void
    {
        $param = new GetDeliveryAddressListParam(lastId: 12345);
        $this->assertSame(12345, $param->lastId);
    }
}
