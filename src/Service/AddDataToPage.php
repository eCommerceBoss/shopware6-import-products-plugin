<?php declare(strict_types=1);

namespace Sas\SyncerModule\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\CountResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Pagelet\Footer\FooterPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Event\StorefrontRenderEvent;

class AddDataToPage implements EventSubscriberInterface
{
	/**
     * @var EntityRepositoryInterface
     */
    private $productConfExtensionRepository;

    public function __construct(EntityRepositoryInterface $productConfExtensionRepository)
    {
        $this->productConfExtensionRepository = $productConfExtensionRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StorefrontRenderEvent::class => "onStorefrontRender"
        ];
    }

    public function onStorefrontRender(StorefrontRenderEvent $event)
    {
        $productId = $event->getRequest()->get("productId");
        $criteria = new Criteria();
	    $criteria->addFilter(new EqualsFilter('product_id', $productId));

	    $productConfExtension = $this->productConfExtensionRepository->search($criteria, $event->getContext())->first();
        if($productConfExtension != null)
        {
	       $event->setParameter('configable', $productConfExtension->getConfigable());
           // $url = "http://109.237.219.217/api/configurator?articlecode=".$articlecode;
            $token = "wHVs3S7yMKtmvPHSVWj99naCnqdX4WaTVwCVT5rp";
            $url = "http://109.237.219.217/api/configurator?articlecode=A00000020";
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
            $result = curl_exec($ch);
            $event->setParameter('testParam', $result);

            curl_close($ch);
        }
    }
}