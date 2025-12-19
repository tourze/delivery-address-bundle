<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class CreateDeliveryAddressParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '收货人姓名')]
        #[Assert\NotBlank]
        public string $consignee,

        #[MethodParam(description: '收货手机号')]
        #[Assert\NotBlank]
        public string $mobile,

        #[MethodParam(description: '省份')]
        #[Assert\NotBlank]
        public string $province,

        #[MethodParam(description: '城市')]
        #[Assert\NotBlank]
        public string $city,

        #[MethodParam(description: '区/县')]
        #[Assert\NotBlank]
        public string $district,

        #[MethodParam(description: '详细地址')]
        #[Assert\NotBlank]
        public string $addressLine,

        #[MethodParam(description: '性别')]
        public ?int $gender = null,

        #[MethodParam(description: '国家')]
        public ?string $country = null,

        #[MethodParam(description: '省份代码')]
        public ?string $provinceCode = null,

        #[MethodParam(description: '城市代码')]
        public ?string $cityCode = null,

        #[MethodParam(description: '区/县代码')]
        public ?string $districtCode = null,

        #[MethodParam(description: '邮编')]
        public ?string $postalCode = null,

        #[MethodParam(description: '地址标签')]
        public ?string $addressTag = null,

        #[MethodParam(description: '是否设为默认')]
        public bool $setDefault = false,
    ) {
    }
}
