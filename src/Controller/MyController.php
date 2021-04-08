<?php declare(strict_types=1);

namespace Sas\SyncerModule\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Sas\SyncerModule\Controller\ProductController;
use Sas\SyncerModule\Service\WritingProductData;
use Shopware\Core\Framework\Context;
use SimpleXMLElement;


/**
 * @RouteScope(scopes={"api"})
 */
class MyController extends AbstractController
{
    /**
     * @var SyncServiceInterface
     */
    private $writingData;
    private $connection;

    public function __construct(WritingProductData $writingData, Connection $connection)
    {
        $this->connection = $connection;
        $this->writingData = $writingData;
    }
     
    /**
     * @Route("/api/v{version}/sas-syncer/my-action-api", name="api.action.sas-syncer.my-action-api", methods={"GET"})
     */
    public function myActionApi(): JsonResponse
    {
        $url = "http://109.237.219.217/api/articlefeed/";
        $token = "wHVs3S7yMKtmvPHSVWj99naCnqdX4WaTVwCVT5rp";
        
        $ch = curl_init();
        $headers = array(
            'token: '.$token
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $xmlString = curl_exec($ch);
        
        $productes = new SimpleXMLElement($xmlString);
        $data = [];
        foreach ($productes->article as $article) {
            $id = (int)$article->articleid;
            $product = [];
            $product['extern_id'] = $id;
            $product['name'] = (string)$article->articledescription;
            $product['product_number'] = (string)$article->articlecode;
            $product['stock'] = (int)$article->articlecurrentstock;
            $price = (string)$article->articlepurchaseprice;
            $price = str_replace(',', '.', $price);
            $product['price'] = number_format((float)$price, 2, '.', '');
            $product['category'] = [];
            foreach ($article->categories->categorie as $categorie) {
                $category = [];
                $category['extern_id'] = (int)$categorie['extern_id'];
                $category['parent_extern_id'] = (int)$categorie['parent_extern_id'];
                $category['name'] = (string)$categorie;
                $product['category'][] = $category;
            }
            $product['property'] = [];
            foreach ($article->attributes->attributegroup as $attributegroup) {
                foreach ($attributegroup->attribute as $attribute) {
                    $property_group_option = [];
                    $property_group_option['extern_id'] = (int)$attribute['extern_id'];
                    $property_group_option['name'] = (string)$attribute['name'];
                    $property_group_option['value'] = (string)$attribute->values->value;
                    $property_group_option['type'] = (string)$attribute['type'];
                    $product['property'][] = $property_group_option; 
                }
            }
            
            $data[] = $product;
        }
        

        foreach ($data as $key => $value) {
            $context = Context::createDefaultContext();
            $this->writingData->writeData($value, $context, $this->connection);
        }        
        
        return new JsonResponse($data);
    }

    private function processProduct( $itemProduct ){

    }

    private function processCategory( $itemCategory ){
    	
    }
}
