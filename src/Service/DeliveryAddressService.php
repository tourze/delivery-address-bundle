<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;

/**
 * 收货地址服务
 */
class DeliveryAddressService
{
    public function __construct(
        private readonly DeliveryAddressRepository $addressRepository,
    ) {
    }

    /**
     * 根据ID和用户获取收货地址
     */
    public function getAddressByIdAndUser(string $addressId, UserInterface $user): ?DeliveryAddress
    {
        return $this->addressRepository->findOneBy([
            'id' => $addressId,
            'user' => $user,
        ]);
    }

    /**
     * 获取用户的默认收货地址
     */
    public function getDefaultAddress(UserInterface $user): ?DeliveryAddress
    {
        return $this->addressRepository->findOneBy([
            'user' => $user,
            'isDefault' => true,
        ]);
    }

    /**
     * 获取用户的所有收货地址
     *
     * @return DeliveryAddress[]
     */
    public function getUserAddresses(UserInterface $user): array
    {
        return $this->addressRepository->findBy(['user' => $user], ['createTime' => 'DESC']);
    }
}
