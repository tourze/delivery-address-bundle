<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: DeliveryAddress::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: DeliveryAddress::class)]
class DeliveryAddressEntityListener
{
    public function prePersist(DeliveryAddress $entity, PrePersistEventArgs $event): void
    {
        // TimestampableAware trait automatically handles timestamps
        if (null === $entity->getCreateTime()) {
            $entity->setCreateTime(new \DateTimeImmutable());
        }
        if (null === $entity->getUpdateTime()) {
            $entity->setUpdateTime(new \DateTimeImmutable());
        }
    }

    public function preUpdate(DeliveryAddress $entity, PreUpdateEventArgs $event): void
    {
        // TimestampableAware trait automatically handles timestamps
        $entity->setUpdateTime(new \DateTimeImmutable());
    }
}
