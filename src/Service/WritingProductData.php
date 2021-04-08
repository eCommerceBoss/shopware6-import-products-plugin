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

    /**
     * @var EntityRepositoryInterface
     */
    private $propertyGroupRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $propertyGroupTranslationRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $propertyGroupOptionRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $propertyGroupOptionTranslationRepository;

    public function __construct(EntityRepositoryInterface $productRepository, EntityRepositoryInterface $taxRepository, EntityRepositoryInterface $categoryRepository, EntityRepositoryInterface $categoryTranslationRepository, EntityRepositoryInterface $propertyGroupRepository, EntityRepositoryInterface $propertyGroupTranslationRepository, EntityRepositoryInterface $propertyGroupOptionRepository, EntityRepositoryInterface $propertyGroupOptionTranslationRepository)
    {
        $this->productRepository = $productRepository;
        $this->taxRepository = $taxRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->propertyGroupRepository = $propertyGroupRepository;
        $this->propertyGroupTranslationRepository = $propertyGroupTranslationRepository;
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
        $this->propertyGroupOptionTranslationRepository = $propertyGroupOptionTranslationRepository;
    }

    public function writeData(Array $data, Context $context, Connection $connection): void
    {
        $category_ids = [];
        $property_group_ids = [];
        $categories = $data['category'];
        $properties = $data['property'];
        //category
        foreach ($categories as $category) {
            $query = $connection->createQueryBuilder();
            $query->select('category_id')->from('category_extension')->where('extern_id = '.$category['extern_id'] );
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result = $statement->fetchAll();
            }
            if(count($result) == 0)
            {

                $query1 = $connection->createQueryBuilder();

                $query1->select('category_id')->from('category_extension')->where('extern_id = '.$category['parent_extern_id'] );
                $statement1 = $query1->execute();

                if ($statement1 instanceof Statement) {
                    $result1 = $statement1->fetchAll();

                }
                $category_id_parent = "";
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
                else
                {
                    $category_id_parent = Uuid::fromBytesToHex($result1[0]['category_id']);
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

                $array = ['category_id' => $category_id, 'name' => $category['name']];
                array_push($category_ids, $array);    
            }
            else
            {
                $category_id = Uuid::fromBytesToHex($result[0]['category_id']);
                $this->categoryRepository->update([
                        [
                            'id' => $category_id,
                            'name' => $category['name']
                        ]
                    ], $context);
                $array = ['category_id' => $category_id, 'name' => $category['name']];
                array_push($category_ids, $array);
            }
        }
        //property
        foreach ($properties as $property) {
            $query = $connection->createQueryBuilder();
            $query->select('property_group_id')->from('property_group_extension')->where('extern_id = '.$property['extern_id'] );
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result = $statement->fetchAll();
            }
            if(count($result) == 0)
            {
                $propertyGroupID = Uuid::randomHex();
                $this->propertyGroupRepository->create([
                    [
                        'id' => $propertyGroupID,
                        'displayType' => $property['type'],
                        'name' => $property['name']
                    ]
                ], $context);

                $this->propertyGroupRepository->upsert([
                    [
                        'id' => $propertyGroupID,
                        'property_group_extension' => ['extern_id' => $property['extern_id']]
                    ]
                ], $context);

                $array = ['groupId' => $propertyGroupID, 'name'=>$property['value']];
                array_push($property_group_ids, $array);
            }
            else
            {
                $propertyGroupID = Uuid::fromBytesToHex($result[0]['property_group_id']);
                $array = ['groupId' => $propertyGroupID, 'name'=>$property['value']];
                array_push($property_group_ids, $array);       
            }
        }
        //product
        $query = $connection->createQueryBuilder();
        $query->select('product_id')->from('product_extension')->where('extern_id = '.$data['extern_id'] );
        $statement = $query->execute();
        if ($statement instanceof Statement) {
            $result = $statement->fetchAll();
        }
        $tax_id = $this->getTaxId($context);
        $tax = $this->taxRepository->search((new Criteria())->addFilter(new EqualsFilter('id', $tax_id)), $context)->first();
        $grossPrice = $data['price']+($data['price']*$tax->getTaxRate())/100;
        $price = [[
                    'linked' => false,
                    'net' => (float) $data['price'],
                    'gross' => (float) $grossPrice,
                    'currencyId' => Defaults::CURRENCY
            ]];
        if(count($result) == 0)
        {
            $productId = Uuid::randomHex();
            $context_default = Context::createDefaultContext();
            $this->productRepository->create([
                [
                    'id' => $productId,
                    'name' => $data['name'],
                    'productNumber' => $data['product_number'],
                    'stock' => $data['stock'],
                    'taxId' => $tax_id,
                    'price' => $price,
                    'categories' => $category_ids,
                    'properties' => $property_group_ids
                ]
            ], $context_default);
            $this->productRepository->upsert([
                [
                    'id' => $productId,
                    'product_extension' =>
                    [
                        'extern_id' =>$data['extern_id']
                    ]
                ]
            ], $context);
        }
        else
        {
            $productId = Uuid::fromBytesToHex($result[0]['product_id']);
            $this->productRepository->delete([
                [
                    'id' => $productId,
                ]
            ], $context);
            $this->productRepository->create([
                [
                    'id' => $productId,
                    'name' => $data['name'],
                    'productNumber' => $data['product_number'],
                    'stock' => $data['stock'],
                    'taxId' => $tax_id,
                    'price' => $price,
                    'categories' => $category_ids,
                    'properties' => $property_group_ids
                ]
            ], $context);
        }
    }

    private function getTaxId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('taxRate', 19.00));

        return $this->taxRepository->searchIds($criteria, $context)->firstId();
    }
}