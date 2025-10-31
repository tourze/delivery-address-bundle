<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\GetDefaultDeliveryAddress;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetDefaultDeliveryAddress::class)]
#[RunTestsInSeparateProcesses]
final class GetDefaultDeliveryAddressTest extends AbstractProcedureTestCase
{
    private GetDefaultDeliveryAddress $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetDefaultDeliveryAddress::class);
    }

    public function testExecuteReturnsDefaultAddress(): void
    {
        $userId = 'user123';

        // 创建用户实体
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 创建默认地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setCountry('中国');
        $address->setProvince('广东省');
        $address->setProvinceCode('440000');
        $address->setCity('深圳市');
        $address->setCityCode('440300');
        $address->setDistrict('南山区');
        $address->setDistrictCode('440305');
        $address->setAddressLine('科技园南路88号');
        $address->setPostalCode('518000');
        $address->setAddressTag('公司');
        $address->setIsDefault(true);

        $this->persistAndFlush($address);
        $addressId = $address->getId();

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertEquals($addressId, $result['id']);
        $this->assertEquals($userId, $result['userId']);
        $this->assertEquals('张三', $result['consignee']);
        $this->assertEquals('13800138000', $result['mobile']);
        $this->assertEquals('中国', $result['country']);
        $this->assertEquals('广东省', $result['province']);
        $this->assertEquals('440000', $result['provinceCode']);
        $this->assertEquals('深圳市', $result['city']);
        $this->assertEquals('440300', $result['cityCode']);
        $this->assertEquals('南山区', $result['district']);
        $this->assertEquals('440305', $result['districtCode']);
        $this->assertEquals('科技园南路88号', $result['addressLine']);
        $this->assertEquals('518000', $result['postalCode']);
        $this->assertEquals('公司', $result['addressTag']);
        $this->assertTrue($result['isDefault']);
        $this->assertArrayHasKey('createdTime', $result);
        $this->assertArrayHasKey('updatedTime', $result);
    }

    public function testExecuteReturnsEmptyArrayWhenNoDefaultAddress(): void
    {
        $userId = 'user123';

        // 创建用户实体
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 创建非默认地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('李四');
        $address->setMobile('13900139000');
        $address->setProvince('北京市');
        $address->setCity('北京市');
        $address->setDistrict('朝阳区');
        $address->setAddressLine('建国门外大街1号');
        $address->setIsDefault(false);

        $this->persistAndFlush($address);

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testExecuteReturnsEmptyArrayWhenUserHasNoAddresses(): void
    {
        $userId = 'user123';

        // 创建用户实体，但不创建任何地址
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testExecuteWithGenderField(): void
    {
        $userId = 'user123';

        // 创建用户实体
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 创建带性别信息的默认地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('王五');
        $address->setMobile('13700137000');
        $address->setProvince('上海市');
        $address->setCity('上海市');
        $address->setDistrict('黄浦区');
        $address->setAddressLine('南京东路100号');
        $address->setIsDefault(true);

        $this->persistAndFlush($address);

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('gender', $result);
        $this->assertArrayHasKey('genderLabel', $result);
    }
}
