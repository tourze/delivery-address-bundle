<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\GBT2261\Gender;
use Tourze\DeliveryAddressBundle\Param\CreateDeliveryAddressParam;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '新增收货地址')]
#[MethodExpose(method: 'CreateDeliveryAddress')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
final class CreateDeliveryAddress extends LockableProcedure
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param CreateDeliveryAddressParam $param
     */
    public function execute(CreateDeliveryAddressParam|RpcParamInterface $param): ArrayResult
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录');
        }

        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee($param->consignee);
        $address->setMobile($param->mobile);
        $address->setCountry($param->country);
        $address->setProvince($param->province);
        $address->setProvinceCode($param->provinceCode);
        $address->setCity($param->city);
        $address->setCityCode($param->cityCode);
        $address->setDistrict($param->district);
        $address->setDistrictCode($param->districtCode);
        $address->setAddressLine($param->addressLine);
        $address->setPostalCode($param->postalCode);
        $address->setAddressTag($param->addressTag);
        $address->setIsDefault(false);

        if (null !== $param->gender) {
            $gender = Gender::tryFrom($param->gender);
            if (null === $gender) {
                throw new ApiException('无效的性别');
            }
            $address->setGender($gender);
        }

        if ($param->setDefault) {
            $this->addressRepository->unsetDefaultForUser($user);
            $address->setIsDefault(true);
        }

        $this->em->persist($address);
        $this->em->flush();

        return new ArrayResult([
            '__message' => '创建成功',
            'id' => $address->getId(),
        ]);
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        $user = $this->security->getUser();

        return ['CreateAddress', $user?->getUserIdentifier() ?? 'anonymous'];
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): ?string
    {
        return null;
    }
}
