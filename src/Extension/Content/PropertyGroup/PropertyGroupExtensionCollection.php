<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\PropertyGroup;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


class PropertyGroupExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return PropertyGroupExtensionEntity::class;
    }
}
