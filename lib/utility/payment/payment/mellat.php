<?php
namespace dash\utility\payment\payment;


class mellat
{

	/**
     * auto save logs
     *
     * @var        boolean
     */
    public static $save_log = false;
    // to save log for this user
    public static $user_id  = null;
    public static $log_data = null;
    public static $payment_response = [];

    /**
     * pay price
     *
     * @param      array  $_args  The arguments
     */
    public static function pay($_args = [])
    {
        $log_meta =
        [
            'data' => self::$log_data,
            'meta' =>
            [
                'args' => func_get_args()
            ],
        ];

        // if soap is not exist return false
        if(!class_exists("soapclient"))
        {
            if(self::$save_log)
            {
                \dash\db\logs::set('payment:mellat:soapclient:not:install', self::$user_id, $log_meta);
            }
            \dash\notif::error(T_("Can not connect to mellat gateway. Install it!"));
            return false;
        }

        try
        {
            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
            ];

            $client    = new \SoapClient('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl', $soap_meta);

            $namespace ='http://interfaces.core.sw.bps.com/';

            $parameters =
            [
                'terminalId'     => $_args['terminalId'],
                'userName'       => $_args['userName'],
                'userPassword'   => $_args['userPassword'],
                'orderId'        => $_args['orderId'],
                'amount'         => $_args['amount'],
                'localDate'      => $_args['localDate'],
                'localTime'      => $_args['localTime'],
                'additionalData' => $_args['additionalData'],
                'callBackUrl'    => $_args['callBackUrl'],
                'payerId'        => $_args['payerId'],
            ];

            $result = $client->__soapCall('bpPayRequest', [$parameters], [$namespace]);

            $return = $result->return;

            $res = explode(',', $return);

            $ResCode = $res[0];

            if ($ResCode == "0")
            {
               return $res[1];
            }
            else
            {
                \dash\notif::error(self::msg($ResCode));
                return false;
            }

        }
        catch (SoapFault $e)
        {
            \dash\db\logs::set('payment:mellat:error:load:web:services', self::$user_id, $log_meta);
            \dash\notif::error(T_("Error in load web services"));
            return false;
        }

    }





    /**
     * { function_description }
     *
     * @param      array  $_args  The arguments
     */
    public static function verify($_args = [])
    {
        $log_meta =
        [
            'data' => self::$log_data,
            'meta' =>
            [
                'args' => func_get_args()
            ],
        ];

        try
        {
            $amount = $_args['amount'];
            unset($_args['amount']);

            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
                // 'cache_wsdl'   => WSDL_CACHE_NONE ,
                // 'encoding'     => 'UTF-8',
            ];
            // $client = new \SoapClient('https://ikc.shaparak.ir/TVerify/Verify.svc', $soap_meta);

            $client = new \SoapClient('https://ikc.shaparak.ir/XVerify/Verify.xml', $soap_meta);

            $result = $client->__soapCall("KicccPaymentsVerification", array($_args));

            self::$payment_response =  (array) $result;

            if(isset($result->KicccPaymentsVerificationResult))
            {
                $result = $result->KicccPaymentsVerificationResult;
            }
            else
            {
                $result = false;
            }

            if(floatval($result) === floatval($amount))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch(Exception $e)
        {
            \dash\db\logs::set('payment:mellat:error:load:web:services:verify', self::$user_id, $log_meta);
            \dash\notif::error(T_("Error in load web services"));
            return false;
        }
    }


    /**
     * set msg
     *
     * @param      <type>  $_status  The status
     */
    public static function msg($_status)
    {

        $msg      = [];
        $msg[0]   = "Transaction Approved";
        $msg[11]  = "Invalid Card Number";
        $msg[12]  = "No Sufficient Funds";
        $msg[13]  = "Incorrect Pin";
        $msg[14]  = "Allowable Number Of Pin Tries Exceeded";
        $msg[15]  = "Card Not Effective";
        $msg[16]  = "Exceeds Withdrawal Frequency Limit";
        $msg[17]  = "Customer Cancellation";
        $msg[18]  = "Expired Card";
        $msg[19]  = "Exceeds Withdrawal Amount Limit";
        $msg[111] = "No Such Issuer";
        $msg[112] = "Card Switch Internal Error";
        $msg[113] = "Issuer Or Switch Is Inoperative";
        $msg[114] = "Transaction Not Permitted To Card Holder";
        $msg[21]  = "Invalid Merchant";
        $msg[23]  = "Security Violation";
        $msg[24]  = "Invalid User Or Password";
        $msg[25]  = "Invalid Amount";
        $msg[31]  = "Invalid Response";
        $msg[32]  = "Format Error";
        $msg[33]  = "No Investment Account";
        $msg[34]  = "System Internal Error";
        $msg[35]  = "Invalid Business Date";
        $msg[41]  = "Duplicate Order Id";
        $msg[42]  = "Sale Transaction Not Found";
        $msg[43]  = "Duplicate Verify";
        $msg[44]  = "Verify Transaction Not Found";
        $msg[45]  = "Transaction Has Been Settled";
        $msg[46]  = "Transaction Has Not Been Settled";
        $msg[47]  = "Settle Transaction Not Found";
        $msg[48]  = "Transaction Has Been Reversed";
        $msg[49]  = "Refund Transaction Not Found";
        $msg[412] = "Bill Digit Incorrect";
        $msg[413] = "Payment Digit Incorrect";
        $msg[414] = "Bill Organization Not Valid";
        $msg[415] = "Session Timeout";
        $msg[416] = "Data Access Exception";
        $msg[417] = "Payer Id Is Invalid";
        $msg[418] = "Customer Not Found";
        $msg[419] = "Try Count Exceeded";
        $msg[421] = "Invalid IP";
        $msg[51]  = "Duplicate Transmission";
        $msg[54]  = "Original Transaction Not Found";
        $msg[55]  = "Invalid Transaction";
        $msg[61]  = "Error In Settle";


        if(isset($msg[$_status]))
        {
            return $msg[$_status];
        }
        else
        {
            return T_("Unkown payment error");
        }
        return $msg;
    }
}
?>