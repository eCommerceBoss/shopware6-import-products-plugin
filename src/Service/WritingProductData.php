<?php declare(strict_types=1);

namespace Sas\SyncerModule\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;

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

    public function writeData(Context $context, Connection $connection): void
    {
        $category_extern_id = 0;
        $query = $connection->createQueryBuilder();
        $query->select('id')->from('Category')->where('extern_id = '.$category_extern_id );
        $statement = $query->execute();
        if ($statement instanceof Statement) {
            $result = $statement->fetchAll();
            // $totalCount = (int) $connection->fetchColumn('SELECT FOUND_ROWS()');
        }
        if(count($result) > 0)
        {
            print_r("ok");
            //update
        }
        else
        {
            print_r("no");
            //insert
        }
        exit;
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'Example product'));

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