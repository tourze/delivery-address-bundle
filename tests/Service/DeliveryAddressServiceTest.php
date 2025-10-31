<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\DeliveryAddressBundle\Service\DeliveryAddressService;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressService::class)]
class DeliveryAddressServiceTest extends TestCase
{
    /** @var MockObject&DeliveryAddressRepository */
    private MockObject $addressRepository;

    private DeliveryAddressService $service;

    /** @var MockObject&UserInterface */
    private MockObject $user;

    protected function setUp(): void
    {
        $this->addressRepository = $this->createMock(DeliveryAddressRepository::class);
        $this->service = new DeliveryAddressService($this->addressRepository);
        $this->user = $this->createMock(UserInterface::class);
    }

    public function testGetAddressByIdAndUser(): void
    {
        $addressId = '123';
        $expectedAddress = $this->createMock(DeliveryAddress::class);

        $this->addressRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $addressId, 'user' => $this->user])
            ->willReturn($expectedAddress)
        ;

        $result = $this->service->getAddressByIdAndUser($addressId, $this->user);

        $this->assertSame($expectedAddress, $result);
    }

    public function testGetAddressByIdAndUserReturnsNull(): void
    {
        $addressId = '123';

        $this->addressRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $addressId, 'user' => $this->user])
            ->willReturn(null)
        ;

        $result = $this->service->getAddressByIdAndUser($addressId, $this->user);

        $this->assertNull($result);
    }

    public function testGetDefaultAddress(): void
    {
        $expectedAddress = $this->createMock(DeliveryAddress::class);

        $this->addressRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['user' => $this->user, 'isDefault' => true])
            ->willReturn($expectedAddress)
        ;

        $result = $this->service->getDefaultAddress($this->user);

        $this->assertSame($expectedAddress, $result);
    }

    public function testGetUserAddresses(): void
    {
        $expectedAddresses = [
            $this->createMock(DeliveryAddress::class),
            $this->createMock(DeliveryAddress::class),
        ];

        $this->addressRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['user' => $this->user], ['createTime' => 'DESC'])
            ->willReturn($expectedAddresses)
        ;

        $result = $this->service->getUserAddresses($this->user);

        $this->assertSame($expectedAddresses, $result);
    }
}
