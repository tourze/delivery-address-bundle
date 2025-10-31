<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '获取默认收货地址')]
#[MethodExpose(method: 'GetDefaultDeliveryAddress')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetDefaultDeliveryAddress extends BaseProcedure
{
    public function __construct(
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $user = $this->security->getUser();
        assert(null !== $user, 'User must be authenticated due to IsGranted annotation');
        $a = $this->addressRepository->findDefaultByUser($user);
        if (!$a instanceof DeliveryAddress) {
            return [];
        }

        return [
            'id' => $a->getId(),
            'userId' => $a->getUser()?->getUserIdentifier(),
            'consignee' => $a->getConsignee(),
            'mobile' => $a->getMobile(),
            'country' => $a->getCountry(),
            'gender' => $a->getGender()?->value,
            'genderLabel' => $a->getGender()?->getLabel(),
            'province' => $a->getProvince(),
            'provinceCode' => $a->getProvinceCode(),
            'city' => $a->getCity(),
            'cityCode' => $a->getCityCode(),
            'district' => $a->getDistrict(),
            'districtCode' => $a->getDistrictCode(),
            'addressLine' => $a->getAddressLine(),
            'postalCode' => $a->getPostalCode(),
            'addressTag' => $a->getAddressTag(),
            'isDefault' => $a->isDefault(),
            'createdTime' => $a->getCreateTime()?->format('Y-m-d H:i:s'),
            'updatedTime' => $a->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
