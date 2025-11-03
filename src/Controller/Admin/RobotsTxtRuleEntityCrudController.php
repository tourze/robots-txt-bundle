<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtRuleEntity;

/**
 * Robots.txt规则管理控制器
 *
 * @extends AbstractCrudController<RobotsTxtRuleEntity>
 */
#[AdminCrud(routePath: '/robots-txt/rule', routeName: 'robots_txt_rule')]
final class RobotsTxtRuleEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RobotsTxtRuleEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Robots.txt规则')
            ->setEntityLabelInPlural('Robots.txt规则列表')
            ->setPageTitle(Crud::PAGE_INDEX, 'Robots.txt规则管理')
            ->setPageTitle(Crud::PAGE_NEW, '创建新规则')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑规则')
            ->setPageTitle(Crud::PAGE_DETAIL, '规则详情')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('userAgent', '用户代理'))
            ->add(NumericFilter::new('priority', '优先级'))
            ->add(EntityFilter::new('entry', '所属条目'))
            ->add(DateTimeFilter::new('createdAt', '创建时间'))
            ->add(DateTimeFilter::new('updatedAt', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('userAgent', '用户代理')
                ->setRequired(true)
                ->setHelp('指定用户代理，例如 * 表示所有爬虫')
                ->setMaxLength(255),

            IntegerField::new('priority', '优先级')
                ->setRequired(true)
                ->setHelp('数字越大优先级越高')
                ->setFormTypeOptions(['attr' => ['min' => 0]]),

            AssociationField::new('directives', '指令列表')
                ->setRequired(false)
                ->setHelp('该规则包含的指令列表'),

            AssociationField::new('entry', '所属条目')
                ->setRequired(false)
                ->setHelp('该规则所属的robots.txt条目'),

            DateTimeField::new('createdAt', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updatedAt', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }
}
