<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\PropertyGroupOption;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PropertyGroupOptionExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('property_group_option_extension', 'id', 'property_group_option_id', PropertyGroupOptionExtensionDefinition::class, true)
        );
    }

    public function getDefinitionClass(): string
    {
        return PropertyGroupOptionDefinition::class;
    }
}