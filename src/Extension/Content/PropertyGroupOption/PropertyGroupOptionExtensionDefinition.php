<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\PropertyGroupOption;

use Shopware\Core\Content\Property\Aggregate\PropertyGroupOptionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PropertyGroupOptionExtensionDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'property_group_option_extension';
    }

    public function getCollectionClass(): string
    {
        return PropertyGroupOptionExtensionCollection::class;
    }

    public function getEntityClass(): string
    {
        return PropertyGroupOptionExtensionEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new FkField('property_group_option_id', 'property_group_option_id', PropertyGroupOptionDefinition::class),
            new IntField('extern_id', 'extern_id'),
        ]);
    }
}