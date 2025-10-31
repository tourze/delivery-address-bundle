<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use TelescopeDjango\Entity\User\User;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;

class DeliveryAddressFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['address', 'test'];
    }

    public function load(ObjectManager $manager): void
    {
        // 为每个地址创建独立的用户
        $users = [];
        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setUserFormatId($i + 1);
            $manager->persist($user);
            $users[] = $user;
        }
        $manager->flush();

        $addressData = [
            [
                'consignee' => '张三',
                'mobile' => '13800138001',
                'country' => '中国',
                'province' => '广东省',
                'city' => '深圳市',
                'district' => '南山区',
                'addressLine' => '科技园南路88号',
                'postalCode' => '518000',
                'addressTag' => '家',
                'isDefault' => true,
                'userIndex' => 0,
            ],
            [
                'consignee' => '李四',
                'mobile' => '13800138002',
                'country' => '中国',
                'province' => '北京市',
                'city' => '北京市',
                'district' => '朝阳区',
                'addressLine' => '建国门外大街1号',
                'postalCode' => '100001',
                'addressTag' => '公司',
                'isDefault' => false,
                'userIndex' => 1,
            ],
            [
                'consignee' => '王五',
                'mobile' => '13800138003',
                'country' => '中国',
                'province' => '上海市',
                'city' => '上海市',
                'district' => '浦东新区',
                'addressLine' => '陆家嘴环路1000号',
                'postalCode' => '200120',
                'addressTag' => '朋友',
                'isDefault' => false,
                'userIndex' => 2,
            ],
        ];

        foreach ($addressData as $index => $data) {
            $address = new DeliveryAddress();
            $address->setUser($users[$data['userIndex']]);
            $address->setConsignee($data['consignee']);
            $address->setMobile($data['mobile']);
            $address->setCountry($data['country']);
            $address->setProvince($data['province']);
            $address->setCity($data['city']);
            $address->setDistrict($data['district']);
            $address->setAddressLine($data['addressLine']);
            $address->setPostalCode($data['postalCode']);
            $address->setAddressTag($data['addressTag']);
            $address->setIsDefault($data['isDefault']);

            $manager->persist($address);
            $this->addReference(sprintf('delivery-address-%d', $index + 1), $address);
        }

        $manager->flush();
    }
}
