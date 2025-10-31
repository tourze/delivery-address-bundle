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
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '更新收货地址')]
#[MethodExpose(method: 'UpdateDeliveryAddress')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class UpdateDeliveryAddress extends LockableProcedure
{
    #[MethodParam(description: '地址ID')]
    #[Assert\Positive]
    public int $addressId;

    #[MethodParam(description: '收货人姓名')]
    public ?string $consignee = null;

    #[MethodParam(description: '收货手机号')]
    public ?string $mobile = null;

    #[MethodParam(description: '性别')]
    public ?int $gender = null;

    #[MethodParam(description: '国家')]
    public ?string $country = null;

    #[MethodParam(description: '省份')]
    public ?string $province = null;

    #[MethodParam(description: '省份代码')]
    public ?string $provinceCode = null;

    #[MethodParam(description: '城市')]
    public ?string $city = null;

    #[MethodParam(description: '城市代码')]
    public ?string $cityCode = null;

    #[MethodParam(description: '区/县')]
    public ?string $district = null;

    #[MethodParam(description: '区/县代码')]
    public ?string $districtCode = null;

    #[MethodParam(description: '详细地址')]
    public ?string $addressLine = null;

    #[MethodParam(description: '邮编')]
    public ?string $postalCode = null;

    #[MethodParam(description: '地址标签')]
    public ?string $addressTag = null;

    #[MethodParam(description: '是否设为默认')]
    public ?bool $setDefault = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
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

        $this->updateAddressFields($address);
        $this->handleDefaultSetting($address);

        $this->em->flush();

        return [
            '__message' => '更新成功',
            'id' => $address->getId(),
        ];
    }

    private function updateAddressFields(DeliveryAddress $address): void
    {
        $this->updateBasicFields($address);
        $this->updateLocationFields($address);
        $this->updateExtraFields($address);
    }

    private function updateBasicFields(DeliveryAddress $address): void
    {
        if (null !== $this->consignee) {
            $address->setConsignee($this->consignee);
        }
        if (null !== $this->mobile) {
            $address->setMobile($this->mobile);
        }
        if (null !== $this->gender) {
            $gender = Gender::tryFrom($this->gender);
            if (null === $gender) {
                throw new ApiException('无效的性别');
            }
            $address->setGender($gender);
        }
        if (null !== $this->country) {
            $address->setCountry($this->country);
        }
    }

    private function updateLocationFields(DeliveryAddress $address): void
    {
        if (null !== $this->province) {
            $address->setProvince($this->province);
        }
        if (null !== $this->provinceCode) {
            $address->setProvinceCode($this->provinceCode);
        }
        if (null !== $this->city) {
            $address->setCity($this->city);
        }
        if (null !== $this->cityCode) {
            $address->setCityCode($this->cityCode);
        }
        if (null !== $this->district) {
            $address->setDistrict($this->district);
        }
        if (null !== $this->districtCode) {
            $address->setDistrictCode($this->districtCode);
        }
    }

    private function updateExtraFields(DeliveryAddress $address): void
    {
        if (null !== $this->addressLine) {
            $address->setAddressLine($this->addressLine);
        }
        if (null !== $this->postalCode) {
            $address->setPostalCode($this->postalCode);
        }
        if (null !== $this->addressTag) {
            $address->setAddressTag($this->addressTag);
        }
    }

    private function handleDefaultSetting(DeliveryAddress $address): void
    {
        if (true === $this->setDefault) {
            $user = $this->security->getUser();
            assert(null !== $user, 'User must be authenticated due to IsGranted annotation');
            $this->addressRepository->unsetDefaultForUser($user);
            $address->setIsDefault(true);
        } elseif (false === $this->setDefault) {
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
