<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\GBT2261\Gender;

/**
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: DeliveryAddressRepository::class)]
#[ORM\Table(name: 'delivery_address', options: ['comment' => '收货地址表'])]
class DeliveryAddress implements \Stringable, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    /** @phpstan-ignore-next-line property.unusedType */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[Assert\Length(max: 64)]
    #[Groups(groups: ['admin_curd'])]
    #[SnowflakeColumn]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, nullable: true, options: ['comment' => '唯一编码'])]
    private ?string $sn = null;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => '收货人姓名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $consignee;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => '收货手机号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $mobile;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '国家'])]
    #[Assert\Length(max: 64)]
    private ?string $country = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '省'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $province;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '省代码'])]
    #[Assert\Length(max: 20)]
    private ?string $provinceCode = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '市'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $city;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '市代码'])]
    #[Assert\Length(max: 20)]
    private ?string $cityCode = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '区/县'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $district;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '区/县代码'])]
    #[Assert\Length(max: 20)]
    private ?string $districtCode = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '详细地址'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $addressLine;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '邮编'])]
    #[Assert\Length(max: 20)]
    private ?string $postalCode = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '地址标签（家/公司等）'])]
    #[Assert\Length(max: 20)]
    private ?string $addressTag = null;

    #[ORM\Column(name: 'is_default', type: Types::BOOLEAN, options: ['comment' => '是否默认地址'])]
    #[IndexColumn]
    #[Assert\NotNull]
    private bool $isDefault = false;

    #[ORM\Column(type: Types::INTEGER, nullable: true, enumType: Gender::class, options: ['comment' => '性别（0:未知 1:男 2:女 9:未说明）'])]
    #[Assert\Choice(callback: [Gender::class, 'cases'])]
    private ?Gender $gender = null;

    public function __toString(): string
    {
        return sprintf('[%s]%s%s%s%s', $this->user?->getUserIdentifier() ?? '-', $this->province ?? '', $this->city ?? '', $this->district ?? '', $this->addressLine ?? '');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsignee(): string
    {
        return $this->consignee;
    }

    public function setConsignee(string $consignee): void
    {
        $this->consignee = $consignee;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function setProvinceCode(?string $provinceCode): void
    {
        $this->provinceCode = $provinceCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCityCode(): ?string
    {
        return $this->cityCode;
    }

    public function setCityCode(?string $cityCode): void
    {
        $this->cityCode = $cityCode;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function setDistrict(string $district): void
    {
        $this->district = $district;
    }

    public function getDistrictCode(): ?string
    {
        return $this->districtCode;
    }

    public function setDistrictCode(?string $districtCode): void
    {
        $this->districtCode = $districtCode;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function setAddressLine(string $addressLine): void
    {
        $this->addressLine = $addressLine;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getAddressTag(): ?string
    {
        return $this->addressTag;
    }

    public function setAddressTag(?string $addressTag): void
    {
        $this->addressTag = $addressTag;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(?string $sn): void
    {
        $this->sn = $sn;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'userId' => $this->user?->getUserIdentifier(),
            'consignee' => $this->getConsignee(),
            'mobile' => $this->getMobile(),
            'gender' => $this->getGender()?->value,
            'genderLabel' => $this->getGender()?->getLabel(),
            'country' => $this->getCountry(),
            'province' => $this->getProvince(),
            'provinceCode' => $this->getProvinceCode(),
            'city' => $this->getCity(),
            'cityCode' => $this->getCityCode(),
            'district' => $this->getDistrict(),
            'districtCode' => $this->getDistrictCode(),
            'addressLine' => $this->getAddressLine(),
            'postalCode' => $this->getPostalCode(),
            'addressTag' => $this->getAddressTag(),
            'isDefault' => $this->isDefault(),
        ];
    }
}
