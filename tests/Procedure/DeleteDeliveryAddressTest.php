<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\DeleteDeliveryAddress;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(DeleteDeliveryAddress::class)]
#[RunTestsInSeparateProcesses]
final class DeleteDeliveryAddressTest extends AbstractProcedureTestCase
{
    private DeleteDeliveryAddress $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(DeleteDeliveryAddress::class);
    }

    public function testExecuteDeletesAddress(): void
    {
        // 创建用户实体并设置认证状态
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);
        $this->setAuthenticatedUser($user);

        // 创建测试地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');

        $this->persistAndFlush($address);
        $addressId = $address->getId();
        self::assertNotNull($addressId);
        $this->procedure->addressId = $addressId;

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('__message', $result);
        $this->assertEquals('删除成功', $result['__message']);

        // 验证地址已被删除
        $this->assertEntityNotExists(DeliveryAddress::class, $addressId);
    }

    public function testExecuteThrowsExceptionWhenAddressNotFound(): void
    {
        // 创建并认证用户
        $user = $this->createNormalUser('user123', 'pass');
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

        $user123 = $this->createNormalUser('user123', 'pass');
        $this->setAuthenticatedUser($user123);

        // 创建属于其他用户的地址
        $address = new DeliveryAddress();
        $address->setUser($user456);
        $address->setConsignee('李四');
        $address->setMobile('13900139000');
        $address->setProvince('北京市');
        $address->setCity('北京市');
        $address->setDistrict('朝阳区');
        $address->setAddressLine('朝阳路123号');

        $this->persistAndFlush($address);
        $addressId = $address->getId();
        self::assertNotNull($addressId);
        $this->procedure->addressId = $addressId;

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('地址不存在');

        $this->procedure->execute();
    }

    public function testGetLockResource(): void
    {
        // 创建并认证用户
        $user = $this->createNormalUser('user123', 'pass');
        $this->setAuthenticatedUser($user);

        $params = new JsonRpcParams([]);
        $resource = $this->procedure->getLockResource($params);

        $this->assertIsArray($resource);
        $this->assertEquals(['DeleteAddress', 'user123'], $resource);
    }
}
