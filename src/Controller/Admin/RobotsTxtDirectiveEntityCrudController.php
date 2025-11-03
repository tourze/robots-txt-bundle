<?php

declare(strict_types=1);

namespace Tourze\RobotsTxtBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Tourze\RobotsTxtBundle\Entity\RobotsTxtDirectiveEntity;

/**
 * Robots.txt指令管理控制器
 *
 * @extends AbstractCrudController<RobotsTxtDirectiveEntity>
 */
#[AdminCrud(routePath: '/robots-txt/directive', routeName: 'robots_txt_directive')]
final class RobotsTxtDirectiveEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RobotsTxtDirectiveEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Robots.txt指令')
            ->setEntityLabelInPlural('Robots.txt指令列表')
            ->setPageTitle(Crud::PAGE_INDEX, 'Robots.txt指令管理')
            ->setPageTitle(Crud::PAGE_NEW, '创建新指令')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑指令')
            ->setPageTitle(Crud::PAGE_DETAIL, '指令详情')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('directive', '指令类型')
                ->setChoices([
                    'Disallow' => 'Disallow',
                    'Allow' => 'Allow',
                    'Crawl-delay' => 'Crawl-delay',
                    'Sitemap' => 'Sitemap',
                ])
            )
            ->add(EntityFilter::new('rule', '所属规则'))
            ->add(DateTimeFilter::new('createdAt', '创建时间'))
            ->add(DateTimeFilter::new('updatedAt', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            ChoiceField::new('directive', '指令类型')
                ->setChoices([
                    'Disallow' => 'Disallow',
                    'Allow' => 'Allow',
                    'Crawl-delay' => 'Crawl-delay',
                    'Sitemap' => 'Sitemap',
                ])
                ->setRequired(true)
                ->setHelp('选择robots.txt指令类型'),

            TextareaField::new('value', '指令值')
                ->setRequired(true)
                ->setHelp('指令对应的值，例如路径或URL'),

            AssociationField::new('rule', '所属规则')
                ->setRequired(false)
                ->setHelp('该指令所属的robots.txt规则'),

            DateTimeField::new('createdAt', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),

            DateTimeField::new('updatedAt', '更新时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }
}
