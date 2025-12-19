<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\DeliveryAddressBundle\Service\DeliveryAddressService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressService::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryAddressServiceTest extends AbstractIntegrationTestCase
{
    private DeliveryAddressRepository $addressRepository;

    private DeliveryAddressService $service;

    private UserInterface $user;

    protected function onSetUp(): void
    {
        $this->addressRepository = self::getService(DeliveryAddressRepository::class);
        $this->service = self::getService(DeliveryAddressService::class);
        $this->user = $this->createNormalUser('testuser', 'password');
    }

    public function testGetAddressByIdAndUser(): void
    {
        $address = new DeliveryAddress();
        $address->setUser($this->user);
        $address->setConsignee('Test User');
        $address->setMobile('13800138000');
        $address->setCountry('中国');
        $address->setProvince('测试省');
        $address->setCity('测试市');
        $address->setDistrict('测试区');
        $address->setAddressLine('测试地址');
        $address->setPostalCode('100000');
        $address->setAddressTag('家');
        $address->setIsDefault(false);

        $this->addressRepository->save($address, true);
        $addressId = (string) $address->getId();

        $result = $this->service->getAddressByIdAndUser($addressId, $this->user);

        $this->assertSame($address, $result);
    }

    public function testGetAddressByIdAndUserReturnsNull(): void
    {
        $result = $this->service->getAddressByIdAndUser('nonexistent-id', $this->user);

        $this->assertNull($result);
    }

    public function testGetDefaultAddress(): void
    {
        $address = new DeliveryAddress();
        $address->setUser($this->user);
        $address->setConsignee('Test User');
        $address->setMobile('13800138000');
        $address->setCountry('中国');
        $address->setProvince('测试省');
        $address->setCity('测试市');
        $address->setDistrict('测试区');
        $address->setAddressLine('测试地址');
        $address->setPostalCode('100000');
        $address->setAddressTag('家');
        $address->setIsDefault(true);

        $this->addressRepository->save($address, true);

        $result = $this->service->getDefaultAddress($this->user);

        $this->assertSame($address, $result);
    }

    public function testGetUserAddresses(): void
    {
        $address1 = new DeliveryAddress();
        $address1->setUser($this->user);
        $address1->setConsignee('Test User 1');
        $address1->setMobile('13800138001');
        $address1->setCountry('中国');
        $address1->setProvince('测试省1');
        $address1->setCity('测试市1');
        $address1->setDistrict('测试区1');
        $address1->setAddressLine('测试地址1');
        $address1->setPostalCode('100001');
        $address1->setAddressTag('家');
        $address1->setIsDefault(false);

        $address2 = new DeliveryAddress();
        $address2->setUser($this->user);
        $address2->setConsignee('Test User 2');
        $address2->setMobile('13800138002');
        $address2->setCountry('中国');
        $address2->setProvince('测试省2');
        $address2->setCity('测试市2');
        $address2->setDistrict('测试区2');
        $address2->setAddressLine('测试地址2');
        $address2->setPostalCode('100002');
        $address2->setAddressTag('公司');
        $address2->setIsDefault(false);

        $this->addressRepository->save($address1, true);
        $this->addressRepository->save($address2, true);

        $result = $this->service->getUserAddresses($this->user);

        $this->assertCount(2, $result);
        $this->assertContains($address1, $result);
        $this->assertContains($address2, $result);
    }
}
