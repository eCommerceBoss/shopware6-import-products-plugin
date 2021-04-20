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
use Sas\SyncerModule\Service\SaveMedia;
use Shopware\Core\Framework\Context;
use SimpleXMLElement;
use Sas\SyncerModule\Config\Config;


/**
 * @RouteScope(scopes={"api"})
 */
class MyController extends AbstractController
{
    /**
     * @var SyncServiceInterface
     */
    private $writingData;
    private $saveMedia;
    private $connection;
    private $config;

    public function __construct(WritingProductData $writingData, Connection $connection, SaveMedia $saveMedia, Config $config)
    {
        $this->connection = $connection;
        $this->writingData = $writingData;
        $this->saveMedia = $saveMedia;
        $this->config = $config;
    }
     
    /**
     * @Route("/api/v{version}/sas-syncer/my-action-api", name="api.action.sas-syncer.my-action-api", methods={"GET"})
     */
    public function myActionApi(): JsonResponse
    {
        $base_url = $this->config->getApiBaseUrl();
        $url = $this->config->getArticleApiUrl();
        $token = $this->config->getApiToken();
        
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
            $product['configable'] = (bool)$article->articleisconfigurable;
            $price = (string)$article->articlepurchaseprice;
            $price = str_replace(',', '.', $price);
            $product['price'] = number_format((float)$price, 2, '.', '');
            $cat_parent_extend_ids = [];
            $cat_by_key = [];
            $product['media'] = [];
            foreach ($article->images->image as $image) {
                $image_url = (string)$image;
                $context = Context::createDefaultContext();
                $url = $base_url.$image_url;
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

                $image_content = curl_exec($ch);
                $mediaId = $this->saveMedia->addImageToProductMedia($image_content, $context);
                array_push($product['media'], $mediaId);

            }
            foreach ($article->categories->categorie as $categorie) {
                $category = [];
                $category['extern_id'] = (int)$categorie['extern_id'];
                $category['parent_extern_id'] = (int)$categorie['parent_extern_id'];
                $category['name'] = (string)$categorie;

                $parent_extern_id = ''.$category['parent_extern_id'];
                $cat_parent_extend_ids[$parent_extern_id] = (int)$categorie['extern_id'];
                $cat_by_key[$parent_extern_id] = $category;
            }

            $key = '0';
            $product['category'] = [];

            if (array_key_exists($key, $cat_parent_extend_ids)) {
                $key_count = 0;
                $product['category'][] = $cat_by_key[$key];
                $key = $cat_parent_extend_ids[$key];
                $key_count++;
                while ( $key_count < count($cat_parent_extend_ids)) {
                    if (!array_key_exists($key, $cat_parent_extend_ids)) {
                        break;
                    }
                    $product['category'][] = $cat_by_key[$key];
                    $key = $cat_parent_extend_ids[$key];
                    $key_count++;
                }
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


}
