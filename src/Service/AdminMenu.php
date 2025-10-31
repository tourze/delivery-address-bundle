<?php

namespace Tourze\DeliveryAddressBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 收货地址菜单服务
 */
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('地址管理')) {
            $item->addChild('地址管理');
        }

        $addressMenu = $item->getChild('地址管理');
        if (null === $addressMenu) {
            return;
        }

        $addressMenu->addChild('收货地址管理')
            ->setUri($this->linkGenerator->getCurdListPage(DeliveryAddress::class))
            ->setAttribute('icon', 'fas fa-map-marker-alt')
        ;
    }
}
