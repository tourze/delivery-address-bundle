<?php

namespace Tourze\DeliveryAddressBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\DeliveryAddressBundle\Service\AdminMenu;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private LinkGeneratorInterface&MockObject $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
    }

    public function testServiceIsCallable(): void
    {
        $service = self::getService(AdminMenu::class);
        // Verify the service implements __invoke method
        $reflection = new \ReflectionClass($service);
        $this->assertTrue($reflection->hasMethod('__invoke'));
        $this->assertTrue($reflection->getMethod('__invoke')->isPublic());
    }

    public function testInvokeCreatesAddressMenu(): void
    {
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->willReturn('/admin/delivery-address')
        ;

        $service = self::getService(AdminMenu::class);
        $rootMenu = $this->createMock(ItemInterface::class);
        $addressManageMenu = $this->createMock(ItemInterface::class);
        $deliveryAddressMenuItem = $this->createMock(ItemInterface::class);

        // 第一次调用返回null（不存在），第二次调用返回子菜单对象
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('地址管理')
            ->willReturnOnConsecutiveCalls(null, $addressManageMenu)
        ;

        $rootMenu->expects($this->once())
            ->method('addChild')
            ->with('地址管理')
            ->willReturn($addressManageMenu)
        ;

        // 设置子菜单的添加期望
        $addressManageMenu->expects($this->once())
            ->method('addChild')
            ->with('收货地址管理')
            ->willReturn($deliveryAddressMenuItem)
        ;

        $deliveryAddressMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/delivery-address')
            ->willReturnSelf()
        ;

        $deliveryAddressMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-map-marker-alt')
            ->willReturnSelf()
        ;

        $service->__invoke($rootMenu);
    }

    public function testInvokeHandlesExistingAddressMenu(): void
    {
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->willReturn('/admin/delivery-address')
        ;

        $service = self::getService(AdminMenu::class);
        $rootMenu = $this->createMock(ItemInterface::class);
        $addressManageMenu = $this->createMock(ItemInterface::class);
        $deliveryAddressMenuItem = $this->createMock(ItemInterface::class);

        // 第一次和第二次调用都返回已存在的子菜单
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('地址管理')
            ->willReturn($addressManageMenu)
        ;

        $rootMenu->expects($this->never())
            ->method('addChild')
        ;

        // 设置子菜单的添加期望
        $addressManageMenu->expects($this->once())
            ->method('addChild')
            ->with('收货地址管理')
            ->willReturn($deliveryAddressMenuItem)
        ;

        $deliveryAddressMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/delivery-address')
            ->willReturnSelf()
        ;

        $deliveryAddressMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-map-marker-alt')
            ->willReturnSelf()
        ;

        $service->__invoke($rootMenu);
    }

    public function testInvokeHandlesNullAddressMenu(): void
    {
        $service = self::getService(AdminMenu::class);
        $rootMenu = $this->createMock(ItemInterface::class);

        // 第一次调用返回null（不存在），第二次调用也返回null（无法创建）
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('地址管理')
            ->willReturnOnConsecutiveCalls(null, null)
        ;

        $rootMenu->expects($this->once())
            ->method('addChild')
            ->with('地址管理')
            ->willReturn($this->createMock(ItemInterface::class))
        ;

        // 由于第二次getChild返回null，方法应该提前返回，不会调用linkGenerator
        $this->linkGenerator->expects($this->never())
            ->method('getCurdListPage')
        ;

        $service->__invoke($rootMenu);
    }
}
