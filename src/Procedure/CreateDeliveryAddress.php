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
#[MethodDoc(summary: '新增收货地址')]
#[MethodExpose(method: 'CreateDeliveryAddress')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class CreateDeliveryAddress extends LockableProcedure
{
    #[MethodParam(description: '收货人姓名')]
    #[Assert\NotBlank]
    public string $consignee;

    #[MethodParam(description: '收货手机号')]
    #[Assert\NotBlank]
    public string $mobile;

    #[MethodParam(description: '性别')]
    public ?int $gender = null;

    #[MethodParam(description: '国家')]
    public ?string $country = null;

    #[MethodParam(description: '省份')]
    #[Assert\NotBlank]
    public string $province;

    #[MethodParam(description: '省份代码')]
    public ?string $provinceCode = null;

    #[MethodParam(description: '城市')]
    #[Assert\NotBlank]
    public string $city;

    #[MethodParam(description: '城市代码')]
    public ?string $cityCode = null;

    #[MethodParam(description: '区/县')]
    #[Assert\NotBlank]
    public string $district;

    #[MethodParam(description: '区/县代码')]
    public ?string $districtCode = null;

    #[MethodParam(description: '详细地址')]
    #[Assert\NotBlank]
    public string $addressLine;

    #[MethodParam(description: '邮编')]
    public ?string $postalCode = null;

    #[MethodParam(description: '地址标签')]
    public ?string $addressTag = null;

    #[MethodParam(description: '是否设为默认')]
    public bool $setDefault = false;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $user = $this->security->getUser();

        $address = new DeliveryAddress();
        $address->setUser($user);
        $address->setConsignee($this->consignee);
        $address->setMobile($this->mobile);
        $address->setCountry($this->country);
        $address->setProvince($this->province);
        $address->setProvinceCode($this->provinceCode);
        $address->setCity($this->city);
        $address->setCityCode($this->cityCode);
        $address->setDistrict($this->district);
        $address->setDistrictCode($this->districtCode);
        $address->setAddressLine($this->addressLine);
        $address->setPostalCode($this->postalCode);
        $address->setAddressTag($this->addressTag);
        $address->setIsDefault(false);

        if (null !== $this->gender) {
            $gender = Gender::tryFrom($this->gender);
            if (null === $gender) {
                throw new ApiException('无效的性别');
            }
            $address->setGender($gender);
        }

        if ($this->setDefault) {
            assert(null !== $user, 'User must be authenticated due to IsGranted annotation');
            $this->addressRepository->unsetDefaultForUser($user);
            $address->setIsDefault(true);
        }

        $this->em->persist($address);
        $this->em->flush();

        return [
            '__message' => '创建成功',
            'id' => $address->getId(),
        ];
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
