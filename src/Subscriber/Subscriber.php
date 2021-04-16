<?php declare(strict_types=1);
namespace Sas\SyncerModule\Subscriber;
 
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
 
class Subscriber implements EventSubscriberInterface
{
    private $orderRepository;
    private $stateMachineStateRepository;
    private $orderAddressRepository;
    private $orderLineItemRepository;
 
    public function __construct(
        EntityRepositoryInterface $orderRepository,
        EntityRepositoryInterface $stateMachineStateRepository,
        EntityRepositoryInterface $orderAddressRepository,
        EntityRepositoryInterface $orderLineItemRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->stateMachineStateRepository = $stateMachineStateRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderLineItemRepository = $orderLineItemRepository;
    }
 
    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_TRANSACTION_WRITTEN_EVENT => 'onOrderTransactionWritten'
        ];
    }
 
    public function onOrderTransactionWritten (EntityWrittenEvent $event)
    {
        $payloads = $event->getPayloads();
        foreach ($payloads as $payload) {
            //get the state of the transaction
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $payload['stateId']));
            $data = $this->stateMachineStateRepository->search($criteria, $event->getContext());
            if ($data) {
                $currentOrderState = $data->first();
 
                //if the transaction state is "paid",
                if ($currentOrderState->getTechnicalName() === OrderTransactionStates::STATE_PAID) {
 
                    //get the ID of the order
                    $orderId = $payload['orderId'];

                    $criteria = new Criteria();
				    $criteria->addFilter(new EqualsFilter('orderId', $orderId));

				    $orderAddress = $this->orderAddressRepository->search($criteria, $context)->first();

				    $criteria = new Criteria();
				    $criteria->addFilter(new EqualsFilter('orderId', $orderId));

				    $order = $this->orderRepository->search($criteria, $context)->first();

				    $criteria = new Criteria();
				    $criteria->addFilter(new EqualsFilter('orderId', $orderId));

				    $orderLineItem = $this->orderLineItemRepository->search($criteria, $context)->first();

				    $key = 'wHVs3S7yMKtmvPHSVWj99naCnqdX4WaTVwCVT5rp';

					// Add customer
					$data = [];
					$data['ExternNumber'] = 123;
					$data['FirstName'] = $orderAddress->getFirstName();
					$data['LastName'] = $orderAddress->getLastName();
					$data['PhoneNumber'] = $orderAddress->getPhoneNumber();
					$data['PostAddress'] = "Straat 12";
					$data['PostHouseNumber'] = $orderAddress->getPhoneNumber();
					$data['PostZipCode'] = $orderAddress->getZipCode();
					$data['PostCity'] = $orderAddress->getCity();
					$data['PostCountryCode'] = "NL";
					$data['VisitAddress'] = "Straat 12";
					$data['VisitHouseNumber'] = "12";
					$data['VisitZipCode'] = $orderAddress->getZipCode();
					$data['VisitCity'] = $orderAddress->getCity();
					$data['VisitCountryCode'] = "NL";

					$postdata = json_encode($data);

					$url = "http://109.237.219.217/api/relation?token=" . $key;

					//setting the curl parameters.
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					        'Content-Type: application/json',
					        'Content-Length: ' . strlen($postdata))
					);
					$result = curl_exec($ch);
					$result = json_decode($result);
					curl_close($ch);


					// Add products
					$data = [];
					$data['RelationId'] = $result->id;
					$data['ExternNumber'] = $order->getOrderNumber(); // Shopware order reference
					$data['PaymentRegistered'] = 'paid'; // Shopware order reference
					$line = [];
					$line['ConfigurationHeaderId'] = "45"; // number from dropdown
					$line['PartPriceWithoutVat'] = 100;
					$line['PartPriceWithVat'] = 121;
					$line['Quantity'] = $orderLineItem->getQuantity();
					$line['VatPercentage'] = 21;
					$line['StockExternArticleCode'] = '9900000026837'; // from product API
					$line['Description'] = $orderLineItem->getDescription();
					$data['OrderLines'][] = $line;

					$postdata = json_encode($data);

					$url = "http://109.237.219.217/api/salesOrder?token=" . $key;

					//setting the curl parameters.
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					        'Content-Type: application/json',
					        'Content-Length: ' . strlen($postdata))
					);
					$result = curl_exec($ch);
					curl_close($ch);
 					
                }
            }
        }
    }
}