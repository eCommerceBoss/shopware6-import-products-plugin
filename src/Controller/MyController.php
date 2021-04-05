<?php declare(strict_types=1);

namespace Sas\SyncerModule\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @RouteScope(scopes={"api"})
 */
class MyController extends AbstractController
{
     
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
	    
	    $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
	    $json = json_encode($xml);
	    $array = json_decode($json,TRUE);

        return new JsonResponse($array);
    }

    private function processProduct( $itemProduct ){

    }

    private function processCategory( $itemCategory ){
    	
    }
}
