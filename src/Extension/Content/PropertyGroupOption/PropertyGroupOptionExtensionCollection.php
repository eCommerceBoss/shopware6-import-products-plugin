<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\PropertyGroupOption;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


class PropertyGroupOptionExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return PropertyGroupOptionExtensionEntity::class;
    }
}
