<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DeliveryAddressBundle\Param\CreateDeliveryAddressParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(CreateDeliveryAddressParam::class)]
final class CreateDeliveryAddressParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new CreateDeliveryAddressParam(
            consignee: '张三',
            mobile: '13800138000',
            province: '广东省',
            city: '深圳市',
            district: '南山区',
            addressLine: '科技园路1号',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('张三', $param->consignee);
        $this->assertSame('13800138000', $param->mobile);
        $this->assertSame('广东省', $param->province);
        $this->assertSame('深圳市', $param->city);
        $this->assertSame('南山区', $param->district);
        $this->assertSame('科技园路1号', $param->addressLine);
    }

    public function testParamWithAllFields(): void
    {
        $param = new CreateDeliveryAddressParam(
            consignee: '李四',
            mobile: '13900139000',
            province: '北京市',
            city: '北京市',
            district: '海淀区',
            addressLine: '中关村大街1号',
            gender: 1,
            country: '中国',
            provinceCode: '110000',
            cityCode: '110100',
            districtCode: '110108',
            postalCode: '100000',
            addressTag: '公司',
            setDefault: true,
        );

        $this->assertSame('李四', $param->consignee);
        $this->assertSame(1, $param->gender);
        $this->assertSame('中国', $param->country);
        $this->assertSame('110000', $param->provinceCode);
        $this->assertSame('100000', $param->postalCode);
        $this->assertSame('公司', $param->addressTag);
        $this->assertTrue($param->setDefault);
    }
}
