<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\Product;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductConfExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('product_conf_extension', 'id', 'product_id', ProductConfExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}