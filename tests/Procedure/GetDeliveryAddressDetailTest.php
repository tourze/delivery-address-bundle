<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\GetDeliveryAddressDetail;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetDeliveryAddressDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetDeliveryAddressDetailTest extends AbstractProcedureTestCase
{
    private GetDeliveryAddressDetail $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetDeliveryAddressDetail::class);
    }

    public function testExecuteReturnsAddressDetail(): void
    {
        $userId = 'user123';

        // 创建用户实体
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 创建测试地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
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

        // 设置参数
        $this->procedure->addressId = (int) $addressId;

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertEquals($addressId, $result['id']);
        $this->assertEquals($userId, $result['userId']);
        $this->assertEquals('张三', $result['consignee']);
        $this->assertEquals('13800138000', $result['mobile']);
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

    public function testExecuteThrowsExceptionWhenAddressNotFound(): void
    {
        $userId = 'user123';

        // 创建用户实体
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        // 设置不存在的地址ID
        $this->procedure->addressId = 99999;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('地址不存在');

        $this->procedure->execute();
    }

    public function testExecuteThrowsExceptionWhenUserNotOwner(): void
    {
        $userId1 = 'user1';
        $userId2 = 'user2';

        // 创建两个用户
        $user1 = $this->createNormalUser($userId1, 'pass');
        $user2 = $this->createNormalUser($userId2, 'pass');
        $this->persistEntities([$user1, $user2]);
        self::getEntityManager()->flush();

        // 为用户1创建地址
        $address = new DeliveryAddress();
        $address->setUser($user1);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');
        $address->setIsDefault(false);

        $this->persistAndFlush($address);
        $addressId = $address->getId();

        // 用用户2登录，尝试访问用户1的地址
        $this->setAuthenticatedUser($user2);

        // 设置地址ID
        $this->procedure->addressId = (int) $addressId;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('地址不存在');

        $this->procedure->execute();
    }
}
