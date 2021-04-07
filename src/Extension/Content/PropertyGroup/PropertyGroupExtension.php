<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\PropertyGroup;
use Shopware\Core\Content\Property\PropertyGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PropertyGroupExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('property_group_extension', 'id', 'property_group_id', PropertyGroupExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return PropertyGroupDefinition::class;
    }
}