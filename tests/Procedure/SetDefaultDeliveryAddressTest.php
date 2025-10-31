<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\SetDefaultDeliveryAddress;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(SetDefaultDeliveryAddress::class)]
#[RunTestsInSeparateProcesses]
final class SetDefaultDeliveryAddressTest extends AbstractProcedureTestCase
{
    private SetDefaultDeliveryAddress $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(SetDefaultDeliveryAddress::class);
    }

    public function testExecuteSetsDefaultAddress(): void
    {
        // 创建用户实体
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);

        // 创建原有默认地址
        $existingDefaultAddress = new DeliveryAddress();
        $existingDefaultAddress->setUser($user);
        $existingDefaultAddress->setConsignee('李四');
        $existingDefaultAddress->setMobile('13900139000');
        $existingDefaultAddress->setProvince('北京市');
        $existingDefaultAddress->setCity('北京市');
        $existingDefaultAddress->setDistrict('朝阳区');
        $existingDefaultAddress->setAddressLine('朝阳路123号');
        $existingDefaultAddress->setIsDefault(true);

        // 创建要设置为默认的地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');
        $address->setIsDefault(false);

        $this->persistEntities([$existingDefaultAddress, $address]);
        self::getEntityManager()->flush();

        $addressId = $address->getId();
        self::assertNotNull($addressId);

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);
        $this->procedure->addressId = $addressId;

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('__message', $result);
        $this->assertEquals('设置成功', $result['__message']);

        // 验证地址已设置为默认
        self::getEntityManager()->refresh($address);
        $this->assertTrue($address->isDefault());

        // 验证原有默认地址已取消默认
        self::getEntityManager()->refresh($existingDefaultAddress);
        $this->assertFalse($existingDefaultAddress->isDefault());
    }

    public function testExecuteThrowsExceptionWhenAddressNotFound(): void
    {
        // 创建用户来设置认证状态
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);
        $this->setAuthenticatedUser($user);

        $this->procedure->addressId = 999;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('地址不存在');

        $this->procedure->execute();
    }

    public function testExecuteThrowsExceptionWhenUserMismatch(): void
    {
        // 创建两个不同的用户
        $user456 = $this->createNormalUser('user456', 'pass');
        $this->persistAndFlush($user456);

        // 创建属于其他用户的地址
        $address = new DeliveryAddress();
        $address->setUser($user456);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');
        $address->setIsDefault(false);

        $this->persistAndFlush($address);
        $addressId = $address->getId();
        self::assertNotNull($addressId);

        // 设置不同的认证用户（user123）来测试用户不匹配的情况
        $user123 = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user123);
        $this->setAuthenticatedUser($user123);
        $this->procedure->addressId = $addressId;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('地址不存在');

        $this->procedure->execute();
    }

    public function testGetLockResource(): void
    {
        // 创建用户并设置认证状态
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);
        $this->setAuthenticatedUser($user);

        $params = new JsonRpcParams([]);
        $resource = $this->procedure->getLockResource($params);

        $this->assertIsArray($resource);
        $this->assertEquals(['SetDefaultAddress', 'user123'], $resource);
    }
}
