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

    public function __construct(EntityRepositoryInterface $productRepository, EntityRepositoryInterface $taxRepository, EntityRepositoryInterface $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function writeData(Context $context, Connection $connection): void
    {
        $category_extern_id = 1;
        // $query = $connection->createQueryBuilder();
        // $query->select('id')->from('Category')->where('extern_id = '.$category_extern_id );
        // $statement = $query->execute();
        // if ($statement instanceof Statement) {
        //     $result = $statement->fetchAll();
        //     // $totalCount = (int) $connection->fetchColumn('SELECT FOUND_ROWS()');
        // }
        $category_id = Uuid::randomHex();
        // if(count($result) == 0)
        // {
            $this->categoryRepository->create([
                [
                    'id' => $category_id,
                    'name' => 'acs'
                ]
            ], $context);

            // $query->update('Category')->set('extern_id', $category_extern_id)->where('id = '.$category_id);
            // $statement = $query->execute();
        // }
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