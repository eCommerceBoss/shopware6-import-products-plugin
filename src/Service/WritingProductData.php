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
    private $productCategoryRepository;

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

    /**
     * @var EntityRepositoryInterface
     */
    private $productPropertyRepository;

    public function __construct(EntityRepositoryInterface $productRepository, EntityRepositoryInterface $productCategoryRepository, EntityRepositoryInterface $taxRepository, EntityRepositoryInterface $categoryRepository, EntityRepositoryInterface $categoryTranslationRepository, EntityRepositoryInterface $propertyGroupRepository, EntityRepositoryInterface $propertyGroupTranslationRepository, EntityRepositoryInterface $propertyGroupOptionRepository, EntityRepositoryInterface $propertyGroupOptionTranslationRepository, EntityRepositoryInterface $productPropertyRepository)
    {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->taxRepository = $taxRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->propertyGroupRepository = $propertyGroupRepository;
        $this->propertyGroupTranslationRepository = $propertyGroupTranslationRepository;
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
        $this->propertyGroupOptionTranslationRepository = $propertyGroupOptionTranslationRepository;
        $this->productPropertyRepository = $productPropertyRepository;
    }

    public function writeData(Array $data, Context $context, Connection $connection): void
    {
        $extern_ids = [];
        $category_ids = [];
        $property_group_ids = [];
        $categories = $data['category'];
        $properties = $data['property'];
        //category
        foreach ($categories as $key => $category) {

            if($key == 0)
            {
                $query = $connection->createQueryBuilder();
                $query->select('category_id')->from('category_extension')->where('extern_id = '.$category['extern_id'] );
                $statement = $query->execute();
                if ($statement instanceof Statement) 
                {
                    $result = $statement->fetchAll();
                }

                if(count($result) == 0)
                {
                    $id = Uuid::randomHex();
                    $temp_category = [
                        "id" => $id,
                        "name" => $category['name'],
                        "taxRate" => 19.00
                    ];
                    $this->categoryRepository->create([
                        $temp_category
                    ], $context);  
                    $this->categoryRepository->update([
                        [
                            'id' => $id,
                            'category_extension' => ['extern_id' => $category['extern_id']]
                        ]
                    ], $context);
                }
                else
                {
                    $id = Uuid::fromBytesToHex($result[0]['category_id']);
                    $temp_category = [
                        "id" => $id,
                        "name" => $category['name'],
                        "taxRate" => 19.00
                    ];
                }
                $array = [
                    "id" => $id, "parentId" => null, "name" => $category['name']
                ];
                array_push($category_ids, $array);
                continue;
            }

            $query = $connection->createQueryBuilder();
            $query->select('category_id')->from('category_extension')->where('extern_id = '.$category['extern_id'] );
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result = $statement->fetchAll();
            }
            if(count($result) == 0)
            {
                $query = $connection->createQueryBuilder();
                $query->select('category_id')->from('category_extension')->where('extern_id = '.$category['parent_extern_id'] );
                $statement = $query->execute();
                if ($statement instanceof Statement) {
                    $result1 = $statement->fetchAll();
                }
                if(count($result1) == 0)
                {
                    $parentId = $temp_category['id'];
                    array_push($extern_ids, $category['parent_extern_id']);
                }
                else
                {
                    $parentId = Uuid::fromBytesToHex($result1[0]['category_id']);
                }

                $categoryId = Uuid::randomHex();
                $this->categoryRepository->create([
                    [
                        'id' => $categoryId,
                        'parentId' => $temp_category['id'],
                        'name' => $category['name']
                    ]
                ], $context);  
                $this->categoryRepository->update([
                    [
                        'id' => $categoryId,
                        'category_extension' => ['extern_id' => $category['extern_id']]
                    ]
                ], $context);
                $array = [
                    "id" => $categoryId, "parentId" => $parentId, "name" => $category['name']
                ];
                array_push($category_ids, $array);
                $temp_category = [
                    "id" => $categoryId,
                    "name" => $category['name'],
                    "taxRate" => 19.00
                ];
            }
            else
            {
                $array = [
                    "id" => Uuid::fromBytesToHex($result[0]['category_id']), "parentId" => $temp_category['id'], "name" => $category['name']
                ];
                array_push($category_ids, $array);
                $temp_category = [
                    "id" => $array['id'],
                    "name" => $category['name'],
                    "taxRate" => 19.00
                ];
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
                $group_optionId = Uuid::randomHex();
                $this->propertyGroupOptionRepository->create([
                    [
                        'id' => $group_optionId,
                        'groupId' => $propertyGroupID,
                        'name' => $property['value']
                    ]
                ], $context);

                $array = ['groupId' => $propertyGroupID,'groupOptionId' => $group_optionId, 'name'=>$property['value']];
                array_push($property_group_ids, $array);
            }
            else
            {
                $propertyGroupID = Uuid::fromBytesToHex($result[0]['property_group_id']);
                $query = $connection->createQueryBuilder();
                $query->select('id')->from('property_group_option')->where('property_group_id = 0x'.$propertyGroupID );
                $statement = $query->execute();
                if ($statement instanceof Statement) {
                    $result1 = $statement->fetchAll();
                }
                if(count($result1) == 0)
                {
                    $group_optionId = Uuid::randomHex();
                    $this->propertyGroupOptionRepository->create([
                        [
                            'id' => $group_optionId,
                            'groupId' => $propertyGroupID,
                            'name' => $property['value']
                        ]
                    ], $context);
                    $array = ['groupId' => $propertyGroupID,'groupOptionId' => $group_optionId, 'name'=>$property['value']];
                    array_push($property_group_ids, $array);
                }
                else
                {
                    foreach ($result1 as $key => $value) {
                        $query = $connection->createQueryBuilder();
                        $query->select('name')->from('property_group_option_translation')->where('property_group_option_id = 0x'.Uuid::fromBytesToHex($value['id']) );
                        $statement = $query->execute();
                        if ($statement instanceof Statement) {
                            $result2 = $statement->fetchAll();
                        }
                        if(count($result2) == 0)
                        {
                            $this->propertyGroupOptionRepository->update([
                                [
                                    'id' => $value['id'],
                                    'groupId' => $propertyGroupID,
                                    'name' => $property['value']
                                ]
                            ], $context);
                        }
                        else
                        {
                            $same = false;
                            foreach ($result2 as $key2 => $value2) {
                                if($property['value'] == $value2['name'])
                                {
                                    $same = true;
                                    break;
                                }
                            }

                            if(!$same)
                            {
                                $this->propertyGroupOptionRepository->update([
                                    [
                                        'id' => Uuid::fromBytesToHex($value['id']),
                                        'groupId' => $propertyGroupID,
                                        'name' => $property['value']
                                    ]
                                ], $context);
                            }
                        }
                    }
                    $array = ['groupId' => $propertyGroupID,'groupOptionId' => Uuid::fromBytesToHex($value['id']), 'name'=>$property['value']];
                    array_push($property_group_ids, $array);
                }       
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
            $this->productRepository->create([
                [
                    'id' => $productId,
                    'name' => $data['name'],
                    'productNumber' => $data['product_number'],
                    'stock' => $data['stock'],
                    'taxId' => $tax_id,
                    'price' => $price
                ]
            ], $context);
            $this->productRepository->update([
                [
                    'id' => $productId,
                    'product_extension' =>
                    [
                        'extern_id' =>$data['extern_id']
                    ]
                ]
            ], $context);
            foreach ($category_ids as $key1 => $value1) {
                $this->productCategoryRepository->create([
                    [
                        'id' => Uuid::randomHex(),
                        'productId' => $productId,
                        'categoryId' => $value1['id']
                    ]
                ], $context);
            }
            foreach ($property_group_ids as $key2 => $value2) {
                $this->productPropertyRepository->create([
                    [
                        'id' => Uuid::randomHex(),
                        'productId' => $productId,
                        'optionId' => $value2['id']
                    ]
                ], $context);
            }
        }
        else
        {
            $productId = Uuid::fromBytesToHex($result[0]['product_id']);
            $query = $connection->createQueryBuilder();
            $query->select('category_id')->from('product_category')->where('product_id = 0x'.$productId );
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result1 = $statement->fetchAll();
            }
            foreach ($result1 as $key => $value) {
                $this->productCategoryRepository->delete([
                [
                    'productId' => $productId,
                    'categoryId' => Uuid::fromBytesToHex($value['category_id'])
                ]
            ], $context);
            }

            $query = $connection->createQueryBuilder();
            $query->select('property_group_option_id')->from('product_property')->where('product_id = 0x'.$productId );
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result2 = $statement->fetchAll();
            }
            foreach ($result2 as $key1 => $value1) {
                $this->productPropertyRepository->delete([
                    [
                        'productId' => $productId,
                        'optionId' => Uuid::fromBytesToHex($value1['property_group_option_id'])
                    ]
                ], $context);
            }
            $this->productRepository->update([
                [
                    'id' => $productId,
                    'name' => $data['name'],
                    'stock' => $data['stock'],
                    'taxId' => $tax_id,
                    'price' => $price
                ]
            ], $context);

            foreach ($category_ids as $key2 => $value2) {
                $this->productCategoryRepository->upsert([
                    [
                        'id' => Uuid::randomHex(),
                        'productId' => $productId,
                        'categoryId' => $value2['id']
                    ]
                ], $context);
            }

            foreach ($property_group_ids as $key3 => $value3) {
                $this->productPropertyRepository->upsert([
                    [
                        'id' => Uuid::randomHex(),
                        'productId' => $productId,
                        'optionId' => $value3['groupOptionId']
                    ]
                ], $context);
            }

        }
    }

    private function getTaxId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('taxRate', 19.00));

        return $this->taxRepository->searchIds($criteria, $context)->firstId();
    }
}