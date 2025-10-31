<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressRepository::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryAddressRepositoryTest extends AbstractRepositoryTestCase
{
    private DeliveryAddressRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = $this->getRepository();

        // 检查当前测试是否需要 DataFixtures 数据
        $currentTest = $this->name();
        if ('testCountWithDataFixtureShouldReturnGreaterThanZero' === $currentTest) {
            // 为计数测试创建测试数据
            $this->createTestDataForCountTest();
        }
    }

    private function createTestDataForCountTest(): void
    {
        $user = $this->createNormalUser('test-fixture-user', 'pass');
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('测试数据用户');
        $address->setMobile('13800138000');
        $address->setCountry('中国');
        $address->setProvince('测试省');
        $address->setCity('测试市');
        $address->setDistrict('测试区');
        $address->setAddressLine('测试地址');
        $address->setPostalCode('100000');
        $address->setAddressTag('测试');
        $address->setIsDefault(true);

        $this->repository->save($address, true);
    }

    protected function createNewEntity(): DeliveryAddress
    {
        $user = $this->createNormalUser('test123', 'pass');
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('测试用户');
        $address->setMobile('13800138000');
        $address->setProvince('测试省');
        $address->setCity('测试市');
        $address->setDistrict('测试区');
        $address->setAddressLine('测试地址');
        $address->setIsDefault(false);

        return $address;
    }

    protected function getRepository(): DeliveryAddressRepository
    {
        return self::getService(DeliveryAddressRepository::class);
    }

    public function testBuildListQueryByUserId(): void
    {
        $user1 = $this->createNormalUser('user123', 'pass');
        $user2 = $this->createNormalUser('user456', 'pass');

        $address1 = new DeliveryAddress();
        $address1->setUser($user1);
        $address1->setConsignee('张三');
        $address1->setMobile('13800138000');
        $address1->setProvince('广东省');
        $address1->setCity('深圳市');
        $address1->setDistrict('南山区');
        $address1->setAddressLine('科技园南路88号');
        $address1->setIsDefault(true);

        $address2 = new DeliveryAddress();
        $address2->setUser($user1);
        $address2->setConsignee('李四');
        $address2->setMobile('13900139000');
        $address2->setProvince('北京市');
        $address2->setCity('北京市');
        $address2->setDistrict('朝阳区');
        $address2->setAddressLine('建国路100号');
        $address2->setIsDefault(false);

        $address3 = new DeliveryAddress();
        $address3->setUser($user2);
        $address3->setConsignee('王五');
        $address3->setMobile('13700137000');
        $address3->setProvince('上海市');
        $address3->setCity('上海市');
        $address3->setDistrict('浦东新区');
        $address3->setAddressLine('世纪大道1号');
        $address3->setIsDefault(true);

        $this->persistEntities([$address1, $address2, $address3]);

        $qb = $this->repository->buildListQueryByUserId('user123');
        $results = $qb->getQuery()->getResult();

        $this->assertIsArray($results);
        $this->assertCount(2, $results);
        $this->assertArrayHasKey(0, $results);
        $this->assertInstanceOf(DeliveryAddress::class, $results[0]);
        $this->assertArrayHasKey(1, $results);
        $this->assertInstanceOf(DeliveryAddress::class, $results[1]);
        $this->assertEquals('user123', $results[0]->getUser()?->getUserIdentifier());
        $this->assertEquals('user123', $results[1]->getUser()?->getUserIdentifier());
        $this->assertTrue($results[0]->isDefault());
    }

    public function testFindDefaultByUserId(): void
    {
        $user = $this->createNormalUser('user123', 'pass');

        $address1 = new DeliveryAddress();
        $address1->setUser($user);
        $address1->setConsignee('张三');
        $address1->setMobile('13800138000');
        $address1->setProvince('广东省');
        $address1->setCity('深圳市');
        $address1->setDistrict('南山区');
        $address1->setAddressLine('科技园南路88号');
        $address1->setIsDefault(true);

        $address2 = new DeliveryAddress();
        $address2->setUser($user);
        $address2->setConsignee('李四');
        $address2->setMobile('13900139000');
        $address2->setProvince('北京市');
        $address2->setCity('北京市');
        $address2->setDistrict('朝阳区');
        $address2->setAddressLine('建国路100号');
        $address2->setIsDefault(false);

        $this->persistEntities([$address1, $address2]);

        $defaultAddress = $this->repository->findDefaultByUserId('user123');

        $this->assertNotNull($defaultAddress);
        $this->assertTrue($defaultAddress->isDefault());
        $this->assertEquals('张三', $defaultAddress->getConsignee());
    }

    public function testFindDefaultByUserIdReturnsNull(): void
    {
        $defaultAddress = $this->repository->findDefaultByUserId('nonexistent');
        $this->assertNull($defaultAddress);
    }

    public function testUnsetDefaultForUser(): void
    {
        $user = $this->createNormalUser('user123', 'pass');

        $address1 = new DeliveryAddress();
        $address1->setUser($user);
        $address1->setConsignee('张三');
        $address1->setMobile('13800138000');
        $address1->setProvince('广东省');
        $address1->setCity('深圳市');
        $address1->setDistrict('南山区');
        $address1->setAddressLine('科技园南路88号');
        $address1->setIsDefault(true);

        $address2 = new DeliveryAddress();
        $address2->setUser($user);
        $address2->setConsignee('李四');
        $address2->setMobile('13900139000');
        $address2->setProvince('北京市');
        $address2->setCity('北京市');
        $address2->setDistrict('朝阳区');
        $address2->setAddressLine('建国路100号');
        $address2->setIsDefault(true);

        $this->persistEntities([$address1, $address2]);

        $affectedRows = $this->repository->unsetDefaultForUser('user123');

        $this->assertEquals(2, $affectedRows);

        self::getEntityManager()->clear();

        $updatedAddress1 = $this->repository->find($address1->getId());
        $updatedAddress2 = $this->repository->find($address2->getId());

        $this->assertNotNull($updatedAddress1);
        $this->assertNotNull($updatedAddress2);
        $this->assertFalse($updatedAddress1->isDefault());
        $this->assertFalse($updatedAddress2->isDefault());
    }

    public function testSave(): void
    {
        $user = $this->createNormalUser('user123', 'pass');
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');
        $address->setIsDefault(false);

        $this->repository->save($address, true);

        $this->assertNotNull($address->getId());

        $savedAddress = $this->repository->find($address->getId());
        $this->assertNotNull($savedAddress);
        $this->assertEquals('张三', $savedAddress->getConsignee());
    }

    public function testRemove(): void
    {
        $user = $this->createNormalUser('user123', 'pass');
        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee('张三');
        $address->setMobile('13800138000');
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');
        $address->setIsDefault(false);

        $this->persistAndFlush($address);
        $id = $address->getId();

        $this->repository->remove($address, true);

        $deletedAddress = $this->repository->find($id);
        $this->assertNull($deletedAddress);
    }
}
