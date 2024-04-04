<?php

namespace Jeishanul\Nagad;

use Jeishanul\Nagad\Utility;

/**
 * Nagad class
 * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
 * @version 1.0.0
 */

class Nagad
{
    private static $nagadHost;
    private static $tnxStatus = false;
    private static $merchantAdditionalInfo = [];

    public static function getUrl()
    {
        date_default_timezone_set('Asia/Dhaka');
        if (config('nagad.sandbox_mode') === 'sandbox') {
            self::$nagadHost = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs";
        } else {
            self::$nagadHost = "https://api.mynagad.com/api/dfs";
        }
    }

    /**
     * Get redirect url <callback url>
     * @param $tnxID integer (default = 1)
     * @param $amount float (default = 10)
     * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
     * @return gateway url
     */
    public static function getRedirectUrl(int $tnxID = 1, float $amount = 10)
    {
        self::getUrl();

        $DateTime = Date('YmdHis');
        $MerchantID = config('nagad.merchant_id');
        $invoiceNo = self::$tnxStatus ? $tnxID : 'Inv' . Date('YmdH') . rand(1000, 10000);
        $merchantCallbackURL = url(config('nagad.callback_url'));

        $SensitiveData = [
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $invoiceNo,
            'challenge' => Utility::generateRandomString()
        ];

        $PostData = array(
            'accountNumber' => config('nagad.merchant_number'),
            'dateTime' => $DateTime,
            'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => Utility::SignatureGenerate(json_encode($SensitiveData))
        );

        $initializeUrl = self::$nagadHost . "/check-out/initialize/" . $MerchantID . "/" . $invoiceNo;

        $Result_Data = Utility::HttpPostMethod($initializeUrl, $PostData);

        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode(Utility::DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomserver = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' => $MerchantID,
                        'orderId' => $invoiceNo,
                        'currencyCode' => '050',
                        'amount' => $amount,
                        'challenge' => $randomserver
                    );

                    if ($tnxID !== '') {
                        self::$merchantAdditionalInfo['tnx_id'] =  $tnxID;
                    }

                    $PostDataOrder = array(
                        'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature' => Utility::SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL' => $merchantCallbackURL,
                        'additionalMerchantInfo' => (object)self::$merchantAdditionalInfo
                    );
                    // order submit
                    $OrderSubmitUrl = self::$nagadHost . "/check-out/complete/" . $paymentReferenceId;
                    $Result_Data_Order = Utility::HttpPostMethod($OrderSubmitUrl, $PostDataOrder);
                    if ($Result_Data_Order['status'] == "Success") {
                        $callBackUrl = ($Result_Data_Order['callBackUrl']);
                        return $callBackUrl;
                    } else {
                        echo json_encode($Result_Data_Order);
                    }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }
    }

    /**
     * Verify Payment
     * @param $tnxID integer (default = 1)
     * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
     * @return array
     */

    public static function verify(): array
    {
        self::getUrl();

        $queryString = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);
        $payment_ref_id = substr($queryString[2], 15);
        $url = self::$nagadHost . "/verify/payment/" . $payment_ref_id;
        $json = Utility::HttpGet($url);
        $arr = json_decode($json, true);
        return $arr;
    }

    /**
     * Get support id for live project <callback url>
     * @param $tnxID integer (default = 1)
     * @author Jeishanul Haque Shishir <shishirjeishanul@gmail.com>
     * @return object support id
     */
    public static function getSupportID(int $tnxID = 1)
    {
        self::getUrl();

        $DateTime = Date('YmdHis');
        $MerchantID = config('nagad.merchant_id');
        $invoiceNo = self::$tnxStatus ? $tnxID : 'Inv' . Date('YmdH') . rand(1000, 10000);

        $SensitiveData = [
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $invoiceNo,
            'challenge' => Utility::generateRandomString()
        ];

        $PostData = array(
            'accountNumber' => config('nagad.merchant_number'),
            'dateTime' => $DateTime,
            'sensitiveData' => Utility::EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => Utility::SignatureGenerate(json_encode($SensitiveData))
        );

        $initializeUrl = self::$nagadHost . "/check-out/initialize/" . $MerchantID . "/" . $invoiceNo;

        $Result_Data = Utility::HttpPostMethodSupportID($initializeUrl, $PostData);

        return $Result_Data;
    }
}
