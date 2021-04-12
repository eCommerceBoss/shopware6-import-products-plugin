<?php declare(strict_types=1);

namespace Sas\SyncerModule\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sas\SyncerModule\Service\WritingProductData;
use Shopware\Core\Framework\Context;
use Doctrine\DBAL\Connection;
use Sas\SyncerModule\Service\SaveMedia;
use SimpleXMLElement;
use Sas\SyncerModule\Config\Config;

class SyncerCommand extends Command
{
    // Command name
    protected static $defaultName = 'syncer-commands:start';

    private $writingData;
    private $saveMedia;

    private $connection;
    private $config;

    public function __construct(WritingProductData $writingData, Connection $connection, SaveMedia $saveMedia, Config $config)
    {
        parent::__construct();
        $this->connection = $connection;
        $this->writingData = $writingData;
        $this->saveMedia = $saveMedia;
        $this->config = $config;
    }

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Does something very special.');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $context = Context::createDefaultContext();
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
            $price = (string)$article->articlepurchaseprice;
            $price = str_replace(',', '.', $price);
            $product['price'] = number_format((float)$price, 2, '.', '');
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

        $output->writeln('');
        $output->writeln('Importing the products started...');

        foreach ($data as $key => $value) {
            $context = Context::createDefaultContext();
            $this->writingData->writeData($value, $context, $this->connection);
            $output->writeln('  '.($key+1) . ': ' . $value['name']);
        }     

        $output->writeln( count($data).' products imported.');

        // Exit code 0 for success
        return 0;
    }
}