<?php 

namespace App\Classes\Shipping;

use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;


class FedExRate {

    private $EndPoint = NULL;

    public function __construct($order) {
        $this->EndPoint = env('FEDEX_END_POINT');

        //authentication & client details
        $rateRequest->WebAuthenticationDetail->UserCredential->Key = env('FEDEX_KEY');
        $rateRequest->WebAuthenticationDetail->UserCredential->Password = env('FEDEX_PASSWORD');
        $rateRequest->ClientDetail->AccountNumber = env('FEDEX_ACCOUNT_NUMBER');
        $rateRequest->ClientDetail->MeterNumber = env('FEDEX_METER_NUMBER');

        $rateRequest->TransactionDetail->CustomerTransactionId = $order['Order_ID'];

        //version
        $rateRequest->Version->ServiceId = 'crs';
        $rateRequest->Version->Major = 24;
        $rateRequest->Version->Minor = 0;
        $rateRequest->Version->Intermediate = 0;

        $rateRequest->ReturnTransitAndCommit = true;

        //shipper
        $rateRequest->RequestedShipment->PreferredCurrency = 'BHD';
        $rateRequest->RequestedShipment->Shipper->Address->StreetLines = ['Office 21 Building 101W Road 11 Block 711 Tubli'];
        $rateRequest->RequestedShipment->Shipper->Address->City = 'Manama';
        $rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = '';
        $rateRequest->RequestedShipment->Shipper->Address->PostalCode = '711';
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = 'BH';

        //recipient
        $rateRequest->RequestedShipment->Recipient->Address->StreetLines = [$order['shipping_address1'].' '.$order['shipping_address2']];
        $rateRequest->RequestedShipment->Recipient->Address->City = $order['city'];
        $rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = '';
        $rateRequest->RequestedShipment->Recipient->Address->PostalCode = '';
        $rateRequest->RequestedShipment->Recipient->Address->CountryCode = $order['country'];


        //shipping charges payment
        $rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;

        //rate request types
        $rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED, SimpleType\RateRequestType::_LIST];

        $rateRequest->RequestedShipment->PackageCount = 2; //$order['package_count'];

        //create package line items
        $rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem(), new ComplexType\RequestedPackageLineItem()];

        //package 1
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = 2;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_KG;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = 10;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = 10;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = 3;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;

        //package 2
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Weight->Value = 5;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Weight->Units = SimpleType\WeightUnits::_LB;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Length = 20;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Width = 20;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Height = 10;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Units = SimpleType\LinearUnits::_IN;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[1]->GroupPackageCount = 1;

    }    
}
