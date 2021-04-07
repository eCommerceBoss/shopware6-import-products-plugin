<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\Category;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


class CategoryExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CategoryExtensionEntity::class;
    }
}
