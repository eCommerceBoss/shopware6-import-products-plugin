<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\Product;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductConfExtensionEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $configable;

    public function getConfigable(): bool
    {
        return $this->configable;
    }

    public function setConfigable(bool $configable): void
    {
        $this->configable = $configable;
    }
}
