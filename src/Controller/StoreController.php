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
use Sas\SyncerModule\Service\CartConfig;
use Shopware\Core\Framework\Context;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteScope(scopes={"storefront"})
 */
class StoreController extends AbstractController
{
    private $cartConfig;

    public function __construct(CartConfig $cartConfig)
    {
        $this->cartConfig = $cartConfig;
    }

    /**
     * @Route("/config", name="frontend.product.configOption", options={"seo"="false"}, methods={"POST"}, defaults={"XmlHttpRequest"=true}))
     */
    public function configOption(Request $request): Response
    {
        $optionId = $request->get('option');

        $token = "wHVs3S7yMKtmvPHSVWj99naCnqdX4WaTVwCVT5rp";
        $url = "http://109.237.219.217/api/saveConfigurator?option=".$optionId;
        $ch = curl_init();
        $headers = array(
            'token: '.$token
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        $this->cartConfig->changePriceOfCart($result);
        return new Response("success");
    }
}