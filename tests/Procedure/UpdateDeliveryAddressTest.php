<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\UpdateDeliveryAddress;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(UpdateDeliveryAddress::class)]
#[RunTestsInSeparateProcesses]
final class UpdateDeliveryAddressTest extends AbstractProcedureTestCase
{
    private UpdateDeliveryAddress $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(UpdateDeliveryAddress::class);
    }

    public function testExecuteUpdatesAddress(): void
    {
        // 创建用户实体
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);

        // 创建初始地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setCountry('中国');
        $address->setProvince('广东省');
        $address->setProvinceCode('44');
        $address->setCity('深圳市');
        $address->setCityCode('4403');
        $address->setDistrict('南山区');
        $address->setDistrictCode('440305');
        $address->setAddressLine('科技园南路88号');
        $address->setPostalCode('518000');
        $address->setAddressTag('家');
        $address->setIsDefault(false);

        $this->persistAndFlush($address);
        $addressId = $address->getId();
        self::assertNotNull($addressId);

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        // 更新地址信息
        $this->procedure->addressId = $addressId;
        $this->procedure->consignee = '李四';
        $this->procedure->mobile = '13900139000';
        $this->procedure->country = '美国';
        $this->procedure->province = '加利福尼亚州';
        $this->procedure->provinceCode = 'CA';
        $this->procedure->city = '旧金山';
        $this->procedure->cityCode = 'SF';
        $this->procedure->district = '市中心';
        $this->procedure->districtCode = 'DT';
        $this->procedure->addressLine = '唐人街123号';
        $this->procedure->postalCode = '94102';
        $this->procedure->addressTag = '公司';
        $this->procedure->setDefault = false;

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('__message', $result);
        $this->assertEquals('更新成功', $result['__message']);

        // 验证地址信息已更新
        self::getEntityManager()->refresh($address);
        $this->assertEquals('李四', $address->getConsignee());
        $this->assertEquals('13900139000', $address->getMobile());
        $this->assertEquals('美国', $address->getCountry());
        $this->assertEquals('加利福尼亚州', $address->getProvince());
        $this->assertEquals('CA', $address->getProvinceCode());
        $this->assertEquals('旧金山', $address->getCity());
        $this->assertEquals('SF', $address->getCityCode());
        $this->assertEquals('市中心', $address->getDistrict());
        $this->assertEquals('DT', $address->getDistrictCode());
        $this->assertEquals('唐人街123号', $address->getAddressLine());
        $this->assertEquals('94102', $address->getPostalCode());
        $this->assertEquals('公司', $address->getAddressTag());
        $this->assertFalse($address->isDefault());
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

        // 设置不同的认证用户（user123）来测试用户不匹配的情况
        $user123 = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user123);
        $this->setAuthenticatedUser($user123);
        $this->procedure->addressId = $addressId;
        $this->procedure->setDefault = true;

        $result = $this->procedure->execute();

        // 验证结果
        self::getEntityManager()->refresh($address);
        $this->assertTrue($address->isDefault());
        $this->assertEquals('更新成功', $result['__message']);

        // 验证原有默认地址已取消默认
        self::getEntityManager()->refresh($existingDefaultAddress);
        $this->assertFalse($existingDefaultAddress->isDefault());
    }

    public function testExecuteUnsetsDefaultAddress(): void
    {
        // 创建用户实体
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);

        // 创建默认地址
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');
        $address->setIsDefault(true);

        $this->persistAndFlush($address);
        $addressId = $address->getId();
        self::assertNotNull($addressId);

        // 设置不同的认证用户（user123）来测试用户不匹配的情况
        $user123 = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user123);
        $this->setAuthenticatedUser($user123);
        $this->procedure->addressId = $addressId;
        $this->procedure->setDefault = false;

        $result = $this->procedure->execute();

        // 验证结果
        self::getEntityManager()->refresh($address);
        $this->assertFalse($address->isDefault());
        $this->assertEquals('更新成功', $result['__message']);
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
        $this->assertEquals(['UpdateAddress', 'user123'], $resource);
    }
}
