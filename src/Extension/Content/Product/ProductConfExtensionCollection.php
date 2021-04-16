<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\Product;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


class ProductConfExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductConfExtensionEntity::class;
    }
}
