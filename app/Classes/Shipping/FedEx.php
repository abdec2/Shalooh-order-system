<?php 

namespace App\Classes\Shipping;

use FedEx\ShipService;
use FedEx\ShipService\ComplexType;
use FedEx\ShipService\SimpleType;

class FedEx {

    private $EndPoint = NULL;

    public function __construct($order)
    {
        // dd($order);
        $line_items = $order['orderData']['line_items'];
        
        $this->EndPoint = env('FEDEX_PRODUCTION_END_POINT');
        $userCredential = new ComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey(env('FEDEX_PRODUCTION_KEY'))
            ->setPassword(env('FEDEX_PRODUCTION_PASSWORD'));

        $webAuthenticationDetail = new ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber(env('FEDEX_PRODUCTION_ACCOUNT_NUMBER'))
            ->setMeterNumber(env('FEDEX_PRODUCTION_METER_NUMBER'));

        $transactionDetail = new ComplexType\TransactionDetail();
        $transactionDetail->setCustomerTransactionId($order['Order_ID']);
        
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
            ->setAccountNumber(env('FEDEX_PRODUCTION_ACCOUNT_NUMBER'))
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
        $specialServiceRequested = new ComplexType\ShipmentSpecialServicesRequested();
        $specialServiceRequested->setSpecialServiceTypes(SimpleType\ShipmentSpecialServiceType::_ELECTRONIC_TRADE_DOCUMENTS);
        $requestedShipment->setSpecialServicesRequested($specialServiceRequested);
        
        $ShippingDocumentFormat = new ComplexType\ShippingDocumentFormat();
        $ShippingDocumentFormat
                    ->setImageType(SimpleType\ShippingDocumentImageType::_PDF)
                    ->setStockType(SimpleType\ShippingDocumentStockType::_PAPER_LETTER)
                    ->setProvideInstructions('1');
        
        $CustomerImageUsage1 = new ComplexType\CustomerImageUsage();
        $CustomerImageUsage1
            ->setType(SimpleType\CustomerImageUsageType::_LETTER_HEAD)
            ->setId('IMAGE_1');

        $CustomerImageUsage2 = new ComplexType\CustomerImageUsage();
        $CustomerImageUsage2
            ->setType(SimpleType\CustomerImageUsageType::_SIGNATURE)
            ->setId('IMAGE_2');

        $CommercialInvoiceDetail = new ComplexType\CommercialInvoiceDetail();
        $CommercialInvoiceDetail
                    ->setFormat($ShippingDocumentFormat)
                    ->setCustomerImageUsages([$CustomerImageUsage1, $CustomerImageUsage2]);

        $ShippingDocumentSpecification = new ComplexType\ShippingDocumentSpecification();
        $ShippingDocumentSpecification->setShippingDocumentTypes([SimpleType\RequestedShippingDocumentType::_COMMERCIAL_INVOICE]);
        $ShippingDocumentSpecification->setCommercialInvoiceDetail($CommercialInvoiceDetail);
        
        $Commodities = [];
        $PriceExShipping = 0;

        foreach($line_items as $product)
        {
            $UnitPrice = new ComplexType\Money();
            $UnitPrice
                    ->setCurrency('BHD')
                    ->setAmount($product['price']);

            $customsValue = new ComplexType\Money();
            $customsValue
                    ->setCurrency('BHD')
                    ->setAmount($product['total']);
            
            $weight = new ComplexType\Weight();
            $weight
                    ->setUnits(SimpleType\WeightUnits::_KG)
                    ->setValue((float)$shipWeight/count($line_items));

            $Commodity = new ComplexType\Commodity();
            $Commodity
                    ->setNumberOfPieces($product['quantity'])
                    ->setDescription($product['name'])
                    ->setCountryOfManufacture('CN')
                    ->setWeight($weight)
                    ->setQuantity($product['quantity'])
                    ->setQuantityUnits('cm')
                    ->setUnitPrice($UnitPrice)
                    ->setCustomsValue($customsValue);
            
            array_push($Commodities, $Commodity);
            $PriceExShipping = $PriceExShipping + (float)$product['total'];
        }
        
        $CustomsClearanceDetail = [
            'DutiesPayment' => new ComplexType\Payment([
              'PaymentType' => 'RECIPIENT', 
            ]),
            'DocumentContent' => 'NON_DOCUMENTS',
            'CustomsValue' => new ComplexType\Money([
              'Currency' => 'BHD',
              'Amount' => $PriceExShipping
            ]),
            'Commodities' => $Commodities,
            'ExportDetail' => new ComplexType\ExportDetail([
              'ExportComplianceStatement' => '30.37(f)'
            ])
          ];

        $requestedShipment->setShippingDocumentSpecification($ShippingDocumentSpecification);
        $requestedShipment->setCustomsClearanceDetail(new ComplexType\CustomsClearanceDetail($CustomsClearanceDetail));
        
        $this->processShipmentRequest = new ComplexType\ProcessShipmentRequest();
        $this->processShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        $this->processShipmentRequest->setClientDetail($clientDetail);
        $this->processShipmentRequest->setTransactionDetail($transactionDetail);
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
        print_r($result->CompletedShipmentDetail->ShipmentDocuments[0]->Parts[0]->Image); die();

        $ship['tracking_number'] = $result->CompletedShipmentDetail->CompletedPackageDetails[0]->TrackingIds[0]->TrackingNumber;
        $ship['file'] = $result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image;
        if(isset($result->CompletedShipmentDetail->ShipmentDocuments[0]->Parts[0]->Image))
        {
            $ship['COMM_INV'] = $result->CompletedShipmentDetail->ShipmentDocuments[0]->Parts[0]->Image;
        }
        // print_r($result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]);die();
        // Save .pdf label
        // file_put_contents('/path/to/label.pdf', $result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image);
        // var_dump($result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image);

        return $ship;

    } // function ends here




} // class ends here