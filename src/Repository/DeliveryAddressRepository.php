<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Repository;

use BizUserBundle\Repository\BizUserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<DeliveryAddress>
 */
#[AsRepository(entityClass: DeliveryAddress::class)]
class DeliveryAddressRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly BizUserRepository $bizUserRepository,
    ) {
        parent::__construct($registry, DeliveryAddress::class);
    }

    public function buildListQueryByUser(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.isDefault', 'DESC')
            ->addOrderBy('a.createTime', 'DESC')
        ;
    }

    public function buildListQueryByUserId(string $userId): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->where('u.username = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.isDefault', 'DESC')
            ->addOrderBy('a.createTime', 'DESC')
        ;
    }

    public function findDefaultByUser(UserInterface $user): ?DeliveryAddress
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.isDefault = :isDefault')
            ->setParameter('user', $user)
            ->setParameter('isDefault', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result instanceof DeliveryAddress ? $result : null;
    }

    public function findDefaultByUserId(string $userId): ?DeliveryAddress
    {
        $result = $this->createQueryBuilder('a')
            ->join('a.user', 'u')
            ->where('u.username = :userId')
            ->andWhere('a.isDefault = :isDefault')
            ->setParameter('userId', $userId)
            ->setParameter('isDefault', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result instanceof DeliveryAddress ? $result : null;
    }

    public function unsetDefaultForUser(UserInterface|string $userOrUserId): int
    {
        if ($userOrUserId instanceof UserInterface) {
            $user = $userOrUserId;
        } else {
            // 通过userIdentifier查找用户实体
            $user = $this->bizUserRepository->findOneBy(['username' => $userOrUserId]);

            if (null === $user) {
                return 0; // 用户不存在，没有需要更新的记录
            }
        }

        $result = $this->createQueryBuilder('a')
            ->update()
            ->set('a.isDefault', ':false')
            ->where('a.user = :user')
            ->setParameter('false', false)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute()
        ;

        return is_int($result) ? $result : 0;
    }

    public function save(DeliveryAddress $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DeliveryAddress $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
