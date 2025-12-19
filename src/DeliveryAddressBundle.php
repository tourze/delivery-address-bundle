<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\JsonRPCCacheBundle\JsonRPCCacheBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCPaginatorBundle\JsonRPCPaginatorBundle;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;

class DeliveryAddressBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            JsonRPCCacheBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            JsonRPCPaginatorBundle::class => ['all' => true],
            JsonRPCSecurityBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            DoctrineIpBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
        ];
    }
}
