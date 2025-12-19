<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Param;

use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetDefaultDeliveryAddressParam implements RpcParamInterface
{
    public function __construct()
    {
    }
}
