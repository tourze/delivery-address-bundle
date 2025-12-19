<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\CreateDeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(CreateDeliveryAddress::class)]
#[RunTestsInSeparateProcesses]
final class CreateDeliveryAddressTest extends AbstractProcedureTestCase
{
    private CreateDeliveryAddress $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(CreateDeliveryAddress::class);
    }

    public function testExecuteCreatesNewAddress(): void
    {
        // 创建用户实体并设置认证状态
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);
        $this->setAuthenticatedUser($user);

        $param = new \Tourze\DeliveryAddressBundle\Param\CreateDeliveryAddressParam(
            consignee: '张三',
            mobile: '13800138000',
            province: '广东省',
            city: '深圳市',
            district: '南山区',
            addressLine: '科技园南路88号',
            country: '中国',
            provinceCode: '44',
            cityCode: '4403',
            districtCode: '440305',
            postalCode: '518000',
            addressTag: '家',
            setDefault: false,
        );

        $result = $this->procedure->execute($param);

        $this->assertInstanceOf(ArrayResult::class, $result);
        $data = $result->toArray();
        $this->assertArrayHasKey('__message', $data);
        $this->assertEquals('创建成功', $data['__message']);

        // 验证数据库中创建了新地址
        $repository = self::getService(DeliveryAddressRepository::class);
        $addresses = $repository->findBy(['user' => $user]);

        $this->assertCount(1, $addresses);

        $address = $addresses[0];
        $this->assertEquals('user123', $address->getUser()?->getUserIdentifier());
        $this->assertEquals('张三', $address->getConsignee());
        $this->assertEquals('13800138000', $address->getMobile());
        $this->assertEquals('中国', $address->getCountry());
        $this->assertEquals('广东省', $address->getProvince());
        $this->assertEquals('44', $address->getProvinceCode());
        $this->assertEquals('深圳市', $address->getCity());
        $this->assertEquals('4403', $address->getCityCode());
        $this->assertEquals('南山区', $address->getDistrict());
        $this->assertEquals('440305', $address->getDistrictCode());
        $this->assertEquals('科技园南路88号', $address->getAddressLine());
        $this->assertEquals('518000', $address->getPostalCode());
        $this->assertEquals('家', $address->getAddressTag());
        $this->assertFalse($address->isDefault());
    }

    public function testExecuteCreatesNewDefaultAddress(): void
    {
        // 创建用户实体并设置认证状态
        $user = $this->createNormalUser('user123', 'pass');
        $this->persistAndFlush($user);
        $this->setAuthenticatedUser($user);

        // 先创建一个已存在的默认地址
        $existingAddress = new DeliveryAddress();
        $existingAddress->setUser($user);
        $existingAddress->setConsignee('李四');
        $existingAddress->setMobile('13900139000');
        $existingAddress->setProvince('北京市');
        $existingAddress->setCity('北京市');
        $existingAddress->setDistrict('朝阳区');
        $existingAddress->setAddressLine('朝阳路123号');
        $existingAddress->setIsDefault(true);

        $this->persistAndFlush($existingAddress);

        // 创建新的默认地址
        $param = new \Tourze\DeliveryAddressBundle\Param\CreateDeliveryAddressParam(
            consignee: '张三',
            mobile: '13800138000',
            province: '广东省',
            city: '深圳市',
            district: '南山区',
            addressLine: '科技园南路88号',
            provinceCode: '44',
            cityCode: '4403',
            districtCode: '440305',
            setDefault: true,
        );

        $result = $this->procedure->execute($param);

        $this->assertInstanceOf(ArrayResult::class, $result);
        $data = $result->toArray();
        $this->assertArrayHasKey('__message', $data);
        $this->assertEquals('创建成功', $data['__message']);

        // 验证新地址为默认地址
        $repository = self::getService(DeliveryAddressRepository::class);
        $newAddress = $repository->findOneBy(['user' => $user, 'consignee' => '张三']);
        $this->assertNotNull($newAddress);
        $this->assertTrue($newAddress->isDefault());

        // 验证原有默认地址已取消默认
        self::getEntityManager()->refresh($existingAddress);
        $this->assertFalse($existingAddress->isDefault());
    }

    public function testGetLockResource(): void
    {
        // 创建并认证用户
        $user = $this->createNormalUser('user123', 'pass');
        $this->setAuthenticatedUser($user);

        $params = new JsonRpcParams([]);
        $resource = $this->procedure->getLockResource($params);

        $this->assertIsArray($resource);
        $this->assertEquals(['CreateAddress', 'user123'], $resource);
    }
}
