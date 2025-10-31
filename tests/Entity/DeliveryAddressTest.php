<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Exception\InvalidUserIdentifierException;
use Tourze\GBT2261\Gender;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DeliveryAddress::class)]
final class DeliveryAddressTest extends AbstractEntityTestCase
{
    protected function createEntity(): DeliveryAddress
    {
        return new DeliveryAddress();
    }

    /**
     * 创建测试用户
     * @phpstan-ignore-next-line
     */
    private function createTestUser(string $userIdentifier = 'test-user'): UserInterface
    {
        if ('' === $userIdentifier) {
            throw new InvalidUserIdentifierException($userIdentifier);
        }

        // 使用简单的匿名类实现，适用于实体测试
        /** @phpstan-ignore-next-line */
        return new class($userIdentifier) implements UserInterface {
            public function __construct(private readonly string $userIdentifier)
            {
            }

            /** @return non-empty-string */
            public function getUserIdentifier(): string
            {
                return '' !== $this->userIdentifier ? $this->userIdentifier : 'default-user';
            }

            /** @return array<string> */
            public function getRoles(): array
            {
                return ['ROLE_USER'];
            }

            public function eraseCredentials(): void
            {
            }
        };
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'consignee' => ['consignee', '张三'],
            'mobile' => ['mobile', '13800138000'],
            'gender' => ['gender', Gender::MAN],
            'country' => ['country', '中国'],
            'province' => ['province', '广东省'],
            'provinceCode' => ['provinceCode', '44'],
            'city' => ['city', '深圳市'],
            'cityCode' => ['cityCode', '4403'],
            'district' => ['district', '南山区'],
            'districtCode' => ['districtCode', '440305'],
            'addressLine' => ['addressLine', '科技园南路88号'],
            'postalCode' => ['postalCode', '518000'],
            'addressTag' => ['addressTag', '家'],
            'createTime' => ['createTime', new \DateTimeImmutable('2024-01-01 10:00:00')],
            'updateTime' => ['updateTime', new \DateTimeImmutable('2024-01-01 11:00:00')],
        ];
    }

    public function testToString(): void
    {
        $address = $this->createEntity();
        $user = $this->createTestUser('user123');
        $address->setUser($user);
        $address->setProvince('广东省');
        $address->setCity('深圳市');
        $address->setDistrict('南山区');
        $address->setAddressLine('科技园南路88号');

        $expected = '[user123]广东省深圳市南山区科技园南路88号';
        $this->assertSame($expected, (string) $address);
    }

    public function testToStringWithNullValues(): void
    {
        $address = $this->createEntity();
        $result = (string) $address;
        $this->assertSame('[-]', $result);
    }

    public function testUpdateTimestamp(): void
    {
        $address = $this->createEntity();
        $address->setCreateTime(new \DateTimeImmutable());
        $address->setUpdateTime(new \DateTimeImmutable());
        $originalUpdateTime = $address->getUpdateTime();

        sleep(1);
        $address->setUpdateTime(new \DateTimeImmutable());

        $this->assertNotEquals($originalUpdateTime, $address->getUpdateTime());
    }
}
