<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class DeleteDeliveryAddressParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '地址ID')]
        #[Assert\Positive]
        public int $addressId,
    ) {
    }
}
