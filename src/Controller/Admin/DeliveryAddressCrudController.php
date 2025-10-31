<?php

declare(strict_types=1);

namespace Tourze\DeliveryAddressBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\DeliveryAddressBundle\Entity\DeliveryAddress;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\GBT2261\Gender;

/**
 * 收货地址管理控制器
 * @extends AbstractCrudController<DeliveryAddress>
 */
#[AdminCrud(routePath: '/delivery-address/address', routeName: 'delivery_address_address')]
final class DeliveryAddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryAddress::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('收货地址')
            ->setEntityLabelInPlural('收货地址管理')
            ->setPageTitle('index', '收货地址列表')
            ->setPageTitle('new', '新建收货地址')
            ->setPageTitle('edit', '编辑收货地址')
            ->setPageTitle('detail', '收货地址详情')
            ->setHelp('index', '管理用户的收货地址信息，包括默认地址设置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['consignee', 'mobile', 'addressLine'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setMaxLength(9999)
        ;

        yield AssociationField::new('user', '用户')
            ->setHelp('关联的用户')
            ->setRequired(true)
        ;

        yield TextField::new('consignee', '收货人姓名')
            ->setHelp('收货人的真实姓名')
            ->setRequired(true)
        ;

        yield TextField::new('mobile', '手机号')
            ->setHelp('收货人的联系手机号')
            ->setRequired(true)
        ;

        $genderField = EnumField::new('gender', '性别');
        $genderField->setEnumCases(Gender::cases());
        $genderField->setHelp('收货人性别');
        $genderField->hideOnIndex();
        yield $genderField;

        yield TextField::new('country', '国家')
            ->setHelp('收货地址的国家信息')
            ->hideOnIndex()
        ;

        yield TextField::new('province', '省')
            ->setHelp('收货地址的省份')
            ->setRequired(true)
        ;

        yield TextField::new('city', '市')
            ->setHelp('收货地址的城市')
            ->setRequired(true)
        ;

        yield TextField::new('district', '区/县')
            ->setHelp('收货地址的区县')
            ->setRequired(true)
        ;

        yield TextField::new('addressLine', '详细地址')
            ->setHelp('具体的门牌号、楼层等详细地址')
            ->setRequired(true)
        ;

        yield TextField::new('postalCode', '邮编')
            ->setHelp('邮政编码')
            ->hideOnIndex()
        ;

        yield TextField::new('addressTag', '地址标签')
            ->setHelp('如：家、公司等标签')
            ->hideOnIndex()
        ;

        yield BooleanField::new('isDefault', '默认地址')
            ->setHelp('是否为用户的默认收货地址')
        ;

        yield TextField::new('fullAddress', '完整地址')
            ->onlyOnDetail()
            ->formatValue(function ($value, DeliveryAddress $entity) {
                return sprintf(
                    '%s%s%s%s%s',
                    null !== $entity->getCountry() ? $entity->getCountry() . ' ' : '',
                    $entity->getProvince(),
                    $entity->getCity(),
                    $entity->getDistrict(),
                    $entity->getAddressLine()
                );
            })
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('consignee', '收货人姓名'))
            ->add(TextFilter::new('mobile', '手机号'))
            ->add(TextFilter::new('province', '省'))
            ->add(TextFilter::new('city', '市'))
            ->add(TextFilter::new('district', '区/县'))
            ->add(BooleanFilter::new('isDefault', '默认地址'))
        ;
    }
}
