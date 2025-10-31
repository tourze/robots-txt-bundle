<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtEntryEntity;

/**
 * Robots.txt条目管理控制器
 */
#[AdminCrud(routePath: '/robots-txt/entry', routeName: 'robots_txt_entry')]
final class RobotsTxtEntryEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RobotsTxtEntryEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Robots.txt条目')
            ->setEntityLabelInPlural('Robots.txt条目列表')
            ->setPageTitle(Crud::PAGE_INDEX, 'Robots.txt条目管理')
            ->setPageTitle(Crud::PAGE_NEW, '创建新条目')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑条目')
            ->setPageTitle(Crud::PAGE_DETAIL, '条目详情')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '条目名称'))
            ->add(BooleanFilter::new('active', '是否激活'))
            ->add(DateTimeFilter::new('createdAt', '创建时间'))
            ->add(DateTimeFilter::new('updatedAt', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('name', '条目名称')
                ->setRequired(true)
                ->setHelp('robots.txt条目的名称标识')
                ->setMaxLength(255),

            TextareaField::new('description', '条目描述')
                ->setRequired(false)
                ->setHelp('对该robots.txt条目的详细描述')
                ->setNumOfRows(3),

            BooleanField::new('active', '是否激活')
                ->setRequired(true)
                ->setHelp('是否启用该robots.txt条目'),

            AssociationField::new('rules', '规则列表')
                ->setRequired(false)
                ->setHelp('该条目包含的规则列表'),

            ArrayField::new('sitemaps', '站点地图')
                ->setRequired(false)
                ->setHelp('站点地图URL列表，每行一个URL'),

            ArrayField::new('comments', '注释')
                ->setRequired(false)
                ->setHelp('注释内容，每行一个注释'),

            DateTimeField::new('createdAt', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updatedAt', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }
}
