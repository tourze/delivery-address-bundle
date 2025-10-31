<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '获取收货地址详情')]
#[MethodExpose(method: 'GetDeliveryAddressDetail')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetDeliveryAddressDetail extends BaseProcedure
{
    #[MethodParam(description: '地址ID')]
    #[Assert\Positive]
    public int $addressId;

    public function __construct(
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $user = $this->security->getUser();
        assert(null !== $user, 'User must be authenticated due to IsGranted annotation');
        $address = $this->addressRepository->find($this->addressId);
        if (null === $address || $address->getUser() !== $user) {
            throw new ApiException('地址不存在');
        }

        return [
            'id' => $address->getId(),
            'userId' => $address->getUser()->getUserIdentifier(),
            'consignee' => $address->getConsignee(),
            'mobile' => $address->getMobile(),
            'gender' => $address->getGender()?->value,
            'genderLabel' => $address->getGender()?->getLabel(),
            'country' => $address->getCountry(),
            'province' => $address->getProvince(),
            'provinceCode' => $address->getProvinceCode(),
            'city' => $address->getCity(),
            'cityCode' => $address->getCityCode(),
            'district' => $address->getDistrict(),
            'districtCode' => $address->getDistrictCode(),
            'addressLine' => $address->getAddressLine(),
            'postalCode' => $address->getPostalCode(),
            'addressTag' => $address->getAddressTag(),
            'isDefault' => $address->isDefault(),
            'createdTime' => $address->getCreateTime()?->format('Y-m-d H:i:s'),
            'updatedTime' => $address->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
