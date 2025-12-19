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
use Tourze\DeliveryAddressBundle\Param\UpdateDeliveryAddressParam;
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
#[MethodDoc(summary: '更新收货地址')]
#[MethodExpose(method: 'UpdateDeliveryAddress')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
final class UpdateDeliveryAddress extends LockableProcedure
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param UpdateDeliveryAddressParam $param
     */
    public function execute(UpdateDeliveryAddressParam|RpcParamInterface $param): ArrayResult
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录');
        }
        $address = $this->addressRepository->find($param->addressId);
        if (null === $address || $address->getUser() !== $user) {
            throw new ApiException('地址不存在');
        }

        $this->updateAddressFields($address, $param);
        $this->handleDefaultSetting($address, $param);

        $this->em->flush();

        return new ArrayResult([
            '__message' => '更新成功',
            'id' => $address->getId(),
        ]);
    }

    private function updateAddressFields(DeliveryAddress $address, UpdateDeliveryAddressParam $param): void
    {
        $this->updateBasicFields($address, $param);
        $this->updateLocationFields($address, $param);
        $this->updateExtraFields($address, $param);
    }

    private function updateBasicFields(DeliveryAddress $address, UpdateDeliveryAddressParam $param): void
    {
        if (null !== $param->consignee) {
            $address->setConsignee($param->consignee);
        }
        if (null !== $param->mobile) {
            $address->setMobile($param->mobile);
        }
        if (null !== $param->gender) {
            $gender = Gender::tryFrom($param->gender);
            if (null === $gender) {
                throw new ApiException('无效的性别');
            }
            $address->setGender($gender);
        }
        if (null !== $param->country) {
            $address->setCountry($param->country);
        }
    }

    private function updateLocationFields(DeliveryAddress $address, UpdateDeliveryAddressParam $param): void
    {
        if (null !== $param->province) {
            $address->setProvince($param->province);
        }
        if (null !== $param->provinceCode) {
            $address->setProvinceCode($param->provinceCode);
        }
        if (null !== $param->city) {
            $address->setCity($param->city);
        }
        if (null !== $param->cityCode) {
            $address->setCityCode($param->cityCode);
        }
        if (null !== $param->district) {
            $address->setDistrict($param->district);
        }
        if (null !== $param->districtCode) {
            $address->setDistrictCode($param->districtCode);
        }
    }

    private function updateExtraFields(DeliveryAddress $address, UpdateDeliveryAddressParam $param): void
    {
        if (null !== $param->addressLine) {
            $address->setAddressLine($param->addressLine);
        }
        if (null !== $param->postalCode) {
            $address->setPostalCode($param->postalCode);
        }
        if (null !== $param->addressTag) {
            $address->setAddressTag($param->addressTag);
        }
    }

    private function handleDefaultSetting(DeliveryAddress $address, UpdateDeliveryAddressParam $param): void
    {
        if (true === $param->setDefault) {
            $user = $this->security->getUser();
            if (null === $user) {
                throw new ApiException('用户未登录');
            }
            $this->addressRepository->unsetDefaultForUser($user);
            $address->setIsDefault(true);
        } elseif (false === $param->setDefault) {
            $address->setIsDefault(false);
        }
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        $user = $this->security->getUser();

        return ['UpdateAddress', $user?->getUserIdentifier() ?? 'anonymous'];
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): ?string
    {
        return null;
    }
}
