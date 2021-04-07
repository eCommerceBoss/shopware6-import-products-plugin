<?php declare(strict_types=1);

namespace Sas\SyncerModule\Extension\Content\PropertyGroupOption;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PropertyGroupOptionExtensionEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var int
     */
    protected $extern_id;

    public function getExternID(): int
    {
        return $this->extern_id;
    }

    public function setExternID(int $extern_id): void
    {
        $this->extern_id = $extern_id;
    }
}
