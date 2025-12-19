<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DeliveryAddressBundle\Repository\DeliveryAddressRepository;
use Tourze\DeliveryAddressBundle\Param\DeleteDeliveryAddressParam;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodTag(name: '收货地址')]
#[MethodDoc(summary: '删除收货地址')]
#[MethodExpose(method: 'DeleteDeliveryAddress')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
final class DeleteDeliveryAddress extends LockableProcedure
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DeliveryAddressRepository $addressRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param DeleteDeliveryAddressParam $param
     */
    public function execute(DeleteDeliveryAddressParam|RpcParamInterface $param): ArrayResult
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new ApiException('用户未登录');
        }
        $address = $this->addressRepository->find($param->addressId);
        if (null === $address || $address->getUser() !== $user) {
            throw new ApiException('地址不存在');
        }

        $this->em->remove($address);
        $this->em->flush();

        return new ArrayResult(['__message' => '删除成功']);
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        $user = $this->security->getUser();

        return ['DeleteAddress', $user?->getUserIdentifier() ?? 'anonymous'];
    }
}
