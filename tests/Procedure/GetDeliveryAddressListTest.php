<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\DeliveryAddressBundle\Procedure\GetDeliveryAddressList;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetDeliveryAddressList::class)]
#[RunTestsInSeparateProcesses]
final class GetDeliveryAddressListTest extends AbstractProcedureTestCase
{
    private GetDeliveryAddressList $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(GetDeliveryAddressList::class);
    }

    public function testExecuteReturnsAddressList(): void
    {
        $userId = 'user123';

        // 创建用户实体
        $user = $this->createNormalUser($userId, 'pass');
        $this->persistAndFlush($user);

        // 创建测试地址
        $address1 = new DeliveryAddress();
        $address1->setUser($user);
        $address1->setConsignee('张三');
        $address1->setMobile('13800138000');
        $address1->setProvince('广东省');
        $address1->setCity('深圳市');
        $address1->setDistrict('南山区');
        $address1->setAddressLine('科技园南路88号');
        $address1->setIsDefault(true);

        $address2 = new DeliveryAddress();
        $address2->setUser($user);
        $address2->setConsignee('李四');
        $address2->setMobile('13900139000');
        $address2->setProvince('北京市');
        $address2->setCity('北京市');
        $address2->setDistrict('朝阳区');
        $address2->setAddressLine('建国路100号');
        $address2->setIsDefault(false);

        $this->persistEntities([$address1, $address2]);
        self::getEntityManager()->flush();

        // 设置认证用户来模拟登录状态
        $this->setAuthenticatedUser($user);

        $payload = $this->assertDeliveryAddressListPayload($this->procedure->execute());
        $this->assertCount(2, $payload['list']);
        $this->assertSame(2, $payload['pagination']['total']);

        $addresses = $payload['list'];
        if (!isset($addresses[0], $addresses[1])) {
            self::fail('地址列表中应存在两条记录以供断言');
        }

        $first = $addresses[0];
        $second = $addresses[1];

        $this->assertSame('张三', $first['consignee']);
        $this->assertSame('李四', $second['consignee']);
        $this->assertTrue($first['isDefault']);
        $this->assertFalse($second['isDefault']);
    }

    /**
     * @param mixed $result
     *
     * @return array{
     *     list: array<int, array{consignee: string, isDefault: bool}>,
     *     pagination: array{total: int, current: int, pageSize: int, hasMore: bool}
     * }
     */
    private function assertDeliveryAddressListPayload(mixed $result): array
    {
        if (!is_array($result)) {
            self::fail('Procedure 返回值必须为数组');
        }

        $this->assertListStructure($result);
        $this->assertPaginationStructure($result);

        /** @var array{
         *     list: array<int, array{consignee: string, isDefault: bool}>,
         *     pagination: array{total: int, current: int, pageSize: int, hasMore: bool}
         * } $result
         */
        return $result;
    }

    /**
     * @param array<mixed> $result
     */
    private function assertListStructure(array $result): void
    {
        if (!array_key_exists('list', $result) || !is_array($result['list'])) {
            self::fail('Procedure 返回值必须包含数组形式的 list 字段');
        }

        if (!array_is_list($result['list'])) {
            self::fail('list 字段应为顺序数组');
        }

        foreach ($result['list'] as $item) {
            $this->assertAddressItemStructure($item);
        }
    }

    /**
     * @param mixed $item
     */
    private function assertAddressItemStructure(mixed $item): void
    {
        if (!is_array($item)) {
            self::fail('地址列表中的每一项都必须是数组');
        }

        if (!array_key_exists('consignee', $item) || !is_string($item['consignee'])) {
            self::fail('地址项必须包含字符串类型的 consignee 字段');
        }

        if (!array_key_exists('isDefault', $item) || !is_bool($item['isDefault'])) {
            self::fail('地址项必须包含布尔类型的 isDefault 字段');
        }
    }

    /**
     * @param array<mixed> $result
     */
    private function assertPaginationStructure(array $result): void
    {
        if (!array_key_exists('pagination', $result) || !is_array($result['pagination'])) {
            self::fail('Procedure 返回值必须包含数组形式的 pagination 字段');
        }

        foreach (['total', 'current', 'pageSize'] as $numericKey) {
            if (!array_key_exists($numericKey, $result['pagination']) || !is_int($result['pagination'][$numericKey])) {
                self::fail(sprintf('pagination.%s 必须存在且为整数', $numericKey));
            }
        }

        if (!array_key_exists('hasMore', $result['pagination']) || !is_bool($result['pagination']['hasMore'])) {
            self::fail('pagination.hasMore 必须存在且为布尔值');
        }
    }
}
