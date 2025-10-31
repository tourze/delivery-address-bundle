<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\EventListener\DeliveryAddressEntityListener;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddressEntityListener::class)]
#[RunTestsInSeparateProcesses]
final class DeliveryAddressEntityListenerTest extends AbstractIntegrationTestCase
{
    private DeliveryAddressEntityListener $listener;

    protected function onSetUp(): void
    {
        $this->listener = self::getService(DeliveryAddressEntityListener::class);
    }

    public function testPrePersist(): void
    {
        $entity = self::getEntityManager()->getClassMetadata(DeliveryAddress::class)->newInstance();
        self::assertInstanceOf(DeliveryAddress::class, $entity);
        $user = $this->createNormalUser('test', 'pass');
        $entity->setUser($user);
        $entity->setConsignee('Test User');
        $entity->setMobile('13800138000');
        $entity->setProvince('Test Province');
        $entity->setCity('Test City');
        $entity->setDistrict('Test District');
        $entity->setAddressLine('Test Address');
        $entity->setIsDefault(false);

        $event = new PrePersistEventArgs($entity, self::getEntityManager());
        $this->listener->prePersist($entity, $event);

        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getCreateTime());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getUpdateTime());
        $this->assertEquals(
            $entity->getCreateTime()->format('Y-m-d H:i:s'),
            $entity->getUpdateTime()->format('Y-m-d H:i:s')
        );
    }

    public function testPreUpdate(): void
    {
        $entity = self::getEntityManager()->getClassMetadata(DeliveryAddress::class)->newInstance();
        self::assertInstanceOf(DeliveryAddress::class, $entity);
        $user = $this->createNormalUser('test', 'pass');
        $entity->setUser($user);
        $entity->setConsignee('Test User');
        $entity->setMobile('13800138000');
        $entity->setProvince('Test Province');
        $entity->setCity('Test City');
        $entity->setDistrict('Test District');
        $entity->setAddressLine('Test Address');
        $entity->setIsDefault(false);

        // TimestampableAware trait will handle timestamps automatically
        $entity->setCreateTime(new \DateTimeImmutable());
        $entity->setUpdateTime(new \DateTimeImmutable());
        $originalUpdatedTime = $entity->getUpdateTime();
        self::assertNotNull($originalUpdatedTime);

        sleep(1);

        $changeSet = ['consignee' => ['Old Name', 'New Name']];
        $event = new PreUpdateEventArgs($entity, self::getEntityManager(), $changeSet);
        $this->listener->preUpdate($entity, $event);

        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getUpdateTime());
        $updateTime = $entity->getUpdateTime();
        $this->assertNotEquals(
            $originalUpdatedTime->format('Y-m-d H:i:s'),
            $updateTime->format('Y-m-d H:i:s')
        );
    }
}
