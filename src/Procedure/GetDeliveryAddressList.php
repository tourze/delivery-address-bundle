<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Param\GetDeliveryAddressListParam;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '获取用户收货地址列表')]
#[MethodExpose(method: 'GetDeliveryAddressList')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
final class GetDeliveryAddressList extends BaseProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param GetDeliveryAddressListParam $param
     */
    public function execute(GetDeliveryAddressListParam|RpcParamInterface $param): ArrayResult
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录');
        }
        $qb = $this->addressRepository->buildListQueryByUser($user);

        return new ArrayResult($this->fetchList(
            $qb,
            fn (DeliveryAddress $a) => $this->formatAddress($a),
            null,
            $param
        ));
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
