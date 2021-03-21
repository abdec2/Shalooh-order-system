<?php 

namespace App\Classes\Shipping;

use FedEx\ShipService;
use FedEx\ShipService\ComplexType;
use FedEx\ShipService\SimpleType;

class FedEx {

    private $EndPoint = NULL;

    public function __construct($order)
    {
        $this->EndPoint = env('FEDEX_END_POINT');
        $userCredential = new ComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey(env('FEDEX_KEY'))
            ->setPassword(env('FEDEX_PASSWORD'));

        $webAuthenticationDetail = new ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
            ->setMeterNumber(env('FEDEX_METER_NUMBER'));

        $version = new ComplexType\VersionId();
        $version
            ->setMajor(26)
            ->setIntermediate(0)
            ->setMinor(0)
            ->setServiceId('ship');

        $shipperAddress = new ComplexType\Address();
        $shipperAddress
            ->setStreetLines(['Office 21 Building 101W Road 11 Block 711 Tubli'])
            ->setCity('Manama')
            ->setStateOrProvinceCode('')
            ->setPostalCode('711')
            ->setCountryCode('BH');

        $shipperContact = new ComplexType\Contact();
        $shipperContact
            ->setCompanyName('Shalooh General Trade')
            ->setEMailAddress('care@shalooh.com')
            ->setPersonName('support')
            ->setPhoneNumber(('0097338101017'));
        
        $shipper = new ComplexType\Party();
        $shipper
            ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
            ->setAddress($shipperAddress)
            ->setContact($shipperContact);


        $recipientAddress = new ComplexType\Address();
        $recipientAddress
            ->setStreetLines([$order['shipping_address1'],$order['shipping_address2']])
            ->setCity($order['city'])
            ->setStateOrProvinceCode('')
            ->setPostalCode('')
            ->setCountryCode($order['country']);
        
        $recipientContact = new ComplexType\Contact();
        $recipientContact
            ->setPersonName($order['first_name'].' '.$order['last_name'])
            ->setPhoneNumber($order['phone']);
        
        $recipient = new ComplexType\Party();
        $recipient
            ->setAddress($recipientAddress)
            ->setContact($recipientContact);
        
        $labelSpecification = new ComplexType\LabelSpecification();
        $labelSpecification
            ->setLabelStockType(new SimpleType\LabelStockType(SimpleType\LabelStockType::_STOCK_4X6))
            ->setImageType(new SimpleType\ShippingDocumentImageType(SimpleType\ShippingDocumentImageType::_PDF))
            ->setLabelFormatType(new SimpleType\LabelFormatType(SimpleType\LabelFormatType::_COMMON2D));
        
        $shipWeight = ((float)$order['orderweight'] < (float)$order['orderVolweight']) ? $order['orderVolweight'] : $order['orderweight'];
        
        $packageLineItem1 = new ComplexType\RequestedPackageLineItem();
        $packageLineItem1
            ->setSequenceNumber(1)
            ->setItemDescription('')
            ->setDimensions(new ComplexType\Dimensions(array(
                'Width' => $order['package_width'],
                'Height' => $order['package_height'],
                'Length' => $order['package_length'],
                'Units' => SimpleType\LinearUnits::_CM
            )))
            ->setWeight(new ComplexType\Weight(array(
                'Value' => $shipWeight,
                'Units' => SimpleType\WeightUnits::_KG
            )));
        
        $shippingChargesPayor = new ComplexType\Payor();
        $shippingChargesPayor->setResponsibleParty($shipper);
        
        $shippingChargesPayment = new ComplexType\Payment();
        $shippingChargesPayment
            ->setPaymentType(SimpleType\PaymentType::_SENDER)
            ->setPayor($shippingChargesPayor);
        $packType = ($shipWeight <= 2.5) ? new SimpleType\PackagingType(SimpleType\PackagingType::_FEDEX_PAK) : new SimpleType\PackagingType(SimpleType\PackagingType::_YOUR_PACKAGING);
        $requestedShipment = new ComplexType\RequestedShipment();
        $requestedShipment->setShipTimestamp(date('c'));
        $requestedShipment->setDropoffType(new SimpleType\DropoffType(SimpleType\DropoffType::_REGULAR_PICKUP));
        $requestedShipment->setServiceType(new SimpleType\ServiceType(SimpleType\ServiceType::_INTERNATIONAL_PRIORITY));
        $requestedShipment->setPackagingType($packType);
        $requestedShipment->setShipper($shipper);
        $requestedShipment->setRecipient($recipient);
        $requestedShipment->setLabelSpecification($labelSpecification);
        $requestedShipment->setRateRequestTypes(array(new SimpleType\RateRequestType(SimpleType\RateRequestType::_PREFERRED)));
        $requestedShipment->setPackageCount(1);
        $requestedShipment->setRequestedPackageLineItems([
            $packageLineItem1
        ]);
        $requestedShipment->setShippingChargesPayment($shippingChargesPayment);
        
        $CustomsClearanceDetail = [
            'DutiesPayment' => new ComplexType\Payment([
              'PaymentType' => 'RECIPIENT', // valid values RECIPIENT, SENDER and THIRD_PARTY
          //    'Payor' => new ComplexType\Payor([
          //      'ResponsibleParty' => new ComplexType\Party([
          //        'AccountNumber' => FEDEX_ACCOUNT_NUMBER, // OPTIONAL  
          //        'Contact' => new ComplexType\Contact([]),
          //        'Address' => new ComplexType\Address([])
          //      ])  
          //    ])
            ]),
            'DocumentContent' => 'NON_DOCUMENTS',
            'CustomsValue' => new ComplexType\Money([
              'Currency' => 'BHD',
              'Amount' => $order['order_amount']
            ]),
            'Commodities' => [
              [
                'NumberOfPieces' => 1,
                'Description' => 'Products from Shalooh.com',
                'CountryOfManufacture' => 'BH',
                'Weight' => array(
                  'Units' => 'KG',
                  'Value' => $shipWeight
                ),
                'Quantity' => 1,
                'QuantityUnits' => 'EA',
                'UnitPrice' => array(
                  'Currency' => 'BHD',
                  'Amount' => 1
                ),
                'CustomsValue' => array(
                  'Currency' => 'BHD',
                  'Amount' => 1
                )
              ]
            ],
            'ExportDetail' => new ComplexType\ExportDetail([
              'B13AFilingOption' => 'NOT_REQUIRED'
            ])
          ];


        $requestedShipment->setCustomsClearanceDetail(new ComplexType\CustomsClearanceDetail($CustomsClearanceDetail));
        
        $this->processShipmentRequest = new ComplexType\ProcessShipmentRequest();
        $this->processShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        $this->processShipmentRequest->setClientDetail($clientDetail);
        $this->processShipmentRequest->setVersion($version);
        $this->processShipmentRequest->setRequestedShipment($requestedShipment);

    } // function ends here

    public function createShipment()
    {
        // print_r($this->processShipmentRequest);
        // die();
        $shipService = new ShipService\Request();
        $shipService->getSoapClient()->__setLocation($this->EndPoint);
        $result = $shipService->getProcessShipmentReply($this->processShipmentRequest);
        print_r($result); die();

        $ship['tracking_number'] = $result->CompletedShipmentDetail->CompletedPackageDetails[0]->TrackingIds[0]->TrackingNumber;
        $ship['file'] = $result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image;
        
        // print_r($result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]);die();
        // Save .pdf label
        // file_put_contents('/path/to/label.pdf', $result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image);
        // var_dump($result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image);

        return $ship;

    } // function ends here




} // class ends here