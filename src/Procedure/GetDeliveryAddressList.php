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
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '获取用户收货地址列表')]
#[MethodExpose(method: 'GetDeliveryAddressList')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetDeliveryAddressList extends BaseProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $user = $this->security->getUser();
        assert(null !== $user, 'User must be authenticated due to IsGranted annotation');
        $qb = $this->addressRepository->buildListQueryByUser($user);

        return $this->fetchList(
            $qb,
            fn (DeliveryAddress $a) => $this->formatAddress($a)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function formatAddress(DeliveryAddress $a): array
    {
        return [
            'id' => $a->getId(),
            'userId' => $a->getUser()?->getUserIdentifier(),
            'consignee' => $a->getConsignee(),
            'mobile' => $a->getMobile(),
            'gender' => $a->getGender()?->value,
            'genderLabel' => $a->getGender()?->getLabel(),
            'country' => $a->getCountry(),
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
