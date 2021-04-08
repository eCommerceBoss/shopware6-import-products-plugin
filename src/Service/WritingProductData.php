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

            $array = ['category_id' => $category_id, 'name' => $category['name']];
            array_push($category_ids, $array);    
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
            $array = ['category_id' => $category_id, 'name' => $category['name']];
            array_push($category_ids, $array);    
        }


        //property
        $properties = [];
        $property_group_option_ids = [];
        $property = [            
                "extern_id" => 3,
                "name" => "Width",
                "type" => "white",
                "value" => "90"
        ];

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

            $propertyGroupOptionID = Uuid::randomHex();
            $this->propertyGroupOptionRepository->create([
                [
                    'id' => $propertyGroupOptionID,
                    'groupId' => $propertyGroupID,
                    'name' => $property['value']
                ]
            ], $context);

            $this->propertyGroupRepository->upsert([
                [
                    'id' => $propertyGroupID,
                    'property_group_extension' => ['extern_id' => $property['extern_id']]
                ]
            ], $context);

            $array = ['groupId' => $propertyGroupOptionID, 'name'=>$property['name'], 'property_group_id'=>$propertyGroupID];
            array_push($property_group_option_ids, $array);
        }
        else
        {
            $propertyGroupID = Uuid::fromBytesToHex($result[0]['property_group_id']);
            $query = $connection->createQueryBuilder();
            $query->select('name')->from('property_group_translation')->where('property_group_id = 0x'.$propertyGroupID );
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result = $statement->fetchAll();
            }
            if($result[0]['name'] != $property['name'])
            {
                $this->propertyGroupRepository->update([
                    [
                        'id' => $propertyGroupID,
                        'displayType' => $property['type'],
                        'name' => $property['name']
                    ]
                ], $context);
            }

            $query = $connection->createQueryBuilder();
            $query->select('property_group_option_id')->from('property_group_option_translation')->where('name = '.$property['value']);
            $statement = $query->execute();
            if ($statement instanceof Statement) {
                $result = $statement->fetchAll();
            }
            if(count($result) == 0)
            {
                $propertyGroupOptionID = Uuid::randomHex();
                $this->propertyGroupOptionRepository->create([
                    [
                        'id' => $propertyGroupOptionID,
                        'groupId' => $propertyGroupID,
                        'name' => $property['value']
                    ]
                ], $context);
                $array = ['groupId' => $propertyGroupOptionID, 'name'=>$property['name'], 'property_group_id'=>$propertyGroupID];
                array_push($property_group_option_ids, $array);       
            }
            else
            {
                $propertyGroupOptionID = Uuid::fromBytesToHex($result[0]['property_group_option_id']);
                $array = ['groupId' => $propertyGroupOptionID, 'name'=>$property['name'], 'property_group_id'=>$propertyGroupID];
                array_push($property_group_option_ids, $array);       
            }

        }


        //product

        $product = [
            "extern_id" => 12312,
            "name" => "IvanCake",
            "media" =>[],
            "price" => 0,
            "property" => $property_group_option_ids,
            "category" => $category_ids,
            'product_number' => "ss788ssds7",
            'stock'=>123
        ];

        $query = $connection->createQueryBuilder();
        $query->select('product_id')->from('product_extension')->where('extern_id = '.$product['extern_id'] );
        $statement = $query->execute();
        if ($statement instanceof Statement) {
            $result = $statement->fetchAll();
        }
        $tax_id = $this->getTaxId($context);
        $tax = $this->taxRepository->search((new Criteria())->addFilter(new EqualsFilter('id', $tax_id)), $context)->first();
        $grossPrice = $product['price']+($product['price']*$tax->getTaxRate())/100;
        $price = [[
                    'linked' => false,
                    'net' => (float) $product['price'],
                    'gross' => (float) $grossPrice,
                    'currencyId' => Defaults::CURRENCY
            ]];
        if(count($result) == 0)
        {
            $productId = Uuid::randomHex();
            $this->productRepository->create([
                [
                    'id' => $productId,
                    'name' => $product['name'],
                    'taxId' => $tax_id,
                    'price'=>$price,
                    'productNumber'=>$product['product_number'],
                    'stock'=>$product['stock']                
                ]
            ], $context);
            $this->productRepository->upsert([
                [
                    'id' => $productId,
                    'categories' => $product['category'],
                    'product_extension' =>
                    [
                        'extern_id' => $product['extern_id']
                    ]
                ]
            ], $context);
        }
        else
        {
            $productId = Uuid::fromBytesToHex($result[0]['product_id']);
            $this->productRepository->update([
                [
                    'id' => $productId,
                    'name' => $product['name'],
                    'taxId' => $tax_id,
                    'price'=>$price,
                    'productNumber'=>$product['product_number'],
                    'stock'=>$product['stock']                
                ]
            ], $context);
            $this->productRepository->upsert([
                [
                    'id' => $productId,
                    'categories' => $product['category'],
                    'product_extension' =>
                    [
                        'extern_id' => $product['extern_id']
                    ]
                ]
            ], $context);
        }

    
        exit;
    }

    private function getTaxId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('taxRate', 19.00));

        return $this->taxRepository->searchIds($criteria, $context)->firstId();
    }
}