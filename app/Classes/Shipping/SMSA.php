<?php 

namespace App\Classes\Shipping;

use Illuminate\Support\Facades\Http;

class SMSA {

    public function SearchByAWBNumber( $awb )
    {
        try{
            $endPoint = env('SMSA_ENDPOINT');
            $username = env('SMSA_EXPRESS_USERNAME');
            $passkey = env('SMSA_EXPRESS_PASSKEY');

            $response = Http::withHeaders([
                "Content-Type" => "text/xml;charset=utf-8",
                "soapaction" => "http://smsaexpress.com/waybills/IWaybillService/GetAwbFileByNumber"
            ])->send("POST", $endPoint, [
                "body" => '<?xml version="1.0" encoding="utf-8"?>
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:way="http://smsaexpress.com/waybills/">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <way:GetAwbFileByNumber>
                            <way:username>'.$username.'</way:username>
                            <way:password>'.$passkey.'</way:password>
                            <way:AwbNumber>'.$awb.'</way:AwbNumber>
                            <way:fileType>2</way:fileType>
                        </way:GetAwbFileByNumber>
                    </soapenv:Body>
                </soapenv:Envelope>'
            ]);

            $data = $response->getBody()->getContents();

            $xmlobj = simplexml_load_string($data);
            $e = $xmlobj->children('s',true)->Body->children()->GetAwbFileByNumberResponse->GetAwbFileByNumberResult->Waybills->SAWB;

            $array = json_decode(json_encode($e),TRUE);

            return $array;
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }
        
    } // function ends here

    public function Generate_SMSA_Waybill_Number_With_File($order)
    {
        try
        {
            $endPoint = env('SMSA_ENDPOINT');
            $username = env('SMSA_EXPRESS_USERNAME');
            $passkey = env('SMSA_EXPRESS_PASSKEY');
            $date= date('Y-m-d').'T'.date('H:i:s');
            $weight = ((float)$order['orderweight'] < (float)$order['orderVolweight']) ? $order['orderVolweight'] : $order['orderweight'];

            $response = Http::withHeaders([
                "Content-Type" => "text/xml;charset=utf-8",
                "soapaction" => "http://smsaexpress.com/waybills/IWaybillService/GenerateAWBWithLabel"
            ])->send("POST", $endPoint, [
                "body" => '<?xml version="1.0" encoding="utf-8"?>
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:way="http://smsaexpress.com/waybills/">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <way:GenerateAWBWithLabel>
                         <way:username>'.$username.'</way:username>
                         <way:password>'.$passkey.'</way:password>
                         <way:Reference>'.$order['Order_ID'].'</way:Reference>
                         <way:senderName>Shalooh General Trading</way:senderName>
                         <way:senderPhone>0097338101017</way:senderPhone>
                         <way:senderAddress1>Office 21 Building 101w Road 11 </way:senderAddress1>
                         <way:senderAddress2>Block 711 Tubli</way:senderAddress2>
                         <way:senderCity>Manama</way:senderCity>
                         <way:senderCountry>BH</way:senderCountry>
                         <way:recName>'.$order['customer_name'].'</way:recName>
                         <way:recPhone>'.$order['phone'].'</way:recPhone>
                         <way:recAddress1>'.$order['shipping_address1'].'</way:recAddress1>
                         <way:recAddress2>'.$order['shipping_address2'].'</way:recAddress2>
                         <way:recCity>'.$order['city'].'</way:recCity>
                         <way:recCountry>'.$order['country'].'</way:recCountry>
                         <way:ShipDate>'.$date.'</way:ShipDate>
                         <way:parcels>1</way:parcels>
                         <way:weight>'.$weight.'</way:weight>
                         <way:weightUnit>KG</way:weightUnit>
                         <way:DV>0</way:DV>
                         <way:ServiceCode>SI</way:ServiceCode>
                         <way:fileType>2</way:fileType>
                      </way:GenerateAWBWithLabel>
                   </soapenv:Body>
                </soapenv:Envelope>'
            ]);

            $data = $response->getBody()->getContents();

            $xmlobj = simplexml_load_string($data);
            $e = $xmlobj->children('s',true)->Body->children()->GenerateAWBWithLabelResponse->children()->GenerateAWBWithLabelResult->Labels->SAWB;


            $array = json_decode(json_encode($e),TRUE);

            return $array;

        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    } // function ends here

} // class ends here 