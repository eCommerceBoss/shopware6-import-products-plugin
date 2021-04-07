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

    /**
     * @var EntityRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $categoryTranslationRepository;

    public function __construct(EntityRepositoryInterface $productRepository, EntityRepositoryInterface $taxRepository, EntityRepositoryInterface $categoryRepository, EntityRepositoryInterface $categoryTranslationRepository)
    {
        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
    }

    public function writeData(Context $context, Connection $connection): void
    {
        //category
        $category_ids = [];
        $category = [
            "extern_id"=>98,
            "parent_extern_id"=>65,
            "name"=>"kjkjkj"
        ];

        $query = $connection->createQueryBuilder();
        $query->select('category_id')->from('category_extension')->where('extern_id = '.$category['extern_id'] );
        $statement = $query->execute();
        if ($statement instanceof Statement) {
            $result = $statement->fetchAll();
        }
        if(count($result) == 0)
        {

            $query1 = $connection->createQueryBuilder();

            $query1->select('category_id')->from('category_extension')->where('extern_id = '.$category['extern_id'] );
            $statement1 = $query1->execute();

            if ($statement1 instanceof Statement) {
                $result1 = $statement1->fetchAll();

            }

            if (count($result1) == 0)
            {
                $category_id_parent = Uuid::randomHex();
                $this->categoryRepository->create([
                    [
                        'id' => $category_id_parent,
                        'name' => 'null'
                    ]
                ], $context);
                $this->categoryRepository->upsert([
                    [
                        'id' => $category_id_parent,
                        'category_extension' => ['extern_id' => $category['parent_extern_id']]
                    ]
                ], $context);
            }

            $category_id = Uuid::randomHex();
                $this->categoryRepository->create([
                    [
                        'id' => $category_id,
                        'parentId' => $category_id_parent,
                        'name' => $category['name']
                    ]
                ], $context);
                $this->categoryRepository->upsert([
                    [
                        'id' => $category_id,
                        'category_extension' => ['extern_id' => $category['extern_id']]
                    ]
                ], $context);

            array_push($category_ids, $category_id);    
        }
        else
        {
            $languageId = $context->getLanguageId();
            $query2 = $connection->createQueryBuilder();
            $category_id = Uuid::fromBytesToHex($result[0]['category_id']);
            $query2->select('name')->from('category_translation')->where('category_id = 0x'.$category_id );
            $statement2 = $query2->execute();
            if ($statement2 instanceof Statement) {
                $result2 = $statement2->fetchAll();
            }
            if($result2[0]['name'] != $category['name'])
            {
                $this->categoryRepository->update([
                    [
                        'id' => $category_id,
                        'name' => $category['name']
                    ]
                ], $context);    
            }

            array_push($category_ids, $category_id);    
        }


        //property
        
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