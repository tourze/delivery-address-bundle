<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

readonly class GetDeliveryAddressListParam implements PaginatorParamInterface
{
    public function __construct(
        #[MethodParam(description: '每页条数')]
        #[Assert\Range(min: 1, max: 2000)]
        public int $pageSize = 10,

        #[MethodParam(description: '当前页数')]
        #[Assert\Range(min: 1, max: 1000)]
        public int $currentPage = 1,

        #[MethodParam(description: '上一次拉取时，最后一条数据的主键ID')]
        public ?int $lastId = null,
    ) {
    }
}
