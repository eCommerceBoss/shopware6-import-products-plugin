<?php declare(strict_types=1);

namespace Sas\SyncerModule\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class WritingProductData
{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $taxRepository;

    public function __construct(EntityRepositoryInterface $productRepository, EntityRepositoryInterface $taxRepository)
    {
        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
    }

    public function writeData(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('extern_id', '0'));

        $productId = $this->productRepository->searchIds($criteria, $context)->firstId();

        $this->productRepository->update([
            [
                'id' => $productId,
                'name' => 'New name'
            ]
        ], $context);
    }

    private function getTaxId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('taxRate', 19.00));

        return $this->taxRepository->searchIds($criteria, $context)->firstId();
    }
}