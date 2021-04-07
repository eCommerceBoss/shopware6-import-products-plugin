<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\Category;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CategoryExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('category_extension', 'id', 'category_id', CategoryExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return CategoryDefinition::class;
    }
}