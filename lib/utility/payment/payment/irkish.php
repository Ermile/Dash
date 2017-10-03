<?php
namespace lib\utility\payment\payment;
use \lib\debug;

class irkish
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
                \lib\db\logs::set('payment:irkish:soapclient:not:install', self::$user_id, $log_meta);
            }
            debug::error(T_("Can not connect to irkish gateway. Install it!"));
            return false;
        }

        try
        {
            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
            ];

            $client = new \SoapClient('https://ikc.shaparak.ir/XToken/Tokens.xml', $soap_meta);


            $result = $client->__soapCall("MakeToken", array($_args));
            if(isset($result->MakeTokenResult->result) && $result->MakeTokenResult->result === true && isset($result->MakeTokenResult->token))
            {
                $token = $result->MakeTokenResult->token;
                \lib\db\logs::set('payment:irkish:redirect', self::$user_id, $log_meta);
                return $token;
            }
            else
            {
                \lib\db\logs::set('payment:irkish:error', self::$user_id, $log_meta);
                debug::error(T_("Error in connecting to bank service"));
                return false;
            }
        }
        catch (SoapFault $e)
        {
            \lib\db\logs::set('payment:irkish:error:load:web:services', self::$user_id, $log_meta);
            debug::error(T_("Error in load web services"));
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
            var_dump($result);exit();
            if(isset($result->KicccPaymentsVerificationResult))
            {
                $result = $result->KicccPaymentsVerificationResult;
            }
            else
            {
                $result = false;
            }

            if(floatval($result) === floatval($_args['amount']))
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
            \lib\db\logs::set('payment:irkish:error:load:web:services:verify', self::$user_id, $log_meta);
            debug::error(T_("Error in load web services"));
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
        $msg = null;
        $T_msg =
        [
            '100' => ['en' => 'Successful', 'fa' => 'عملیات موفق می باشد',],
            '110' => ['en' => 'Transaction canceled', 'fa' => 'تراکنش لغو شد',],
            '-20' => [ 'en' => "در درخواست کارکتر های غیر مجاز وجو دارد",  'fa' => "در درخواست کارکتر های غیر مجاز وجو دارد",],
            '-30' => [ 'en' => "تراکنش قبلا برگشت خورده است",  'fa' => "تراکنش قبلا برگشت خورده است",],
            '-50' => [ 'en' => "طول رشته درخواست غیر مجاز است",  'fa' => "طول رشته درخواست غیر مجاز است",],
            '-51' => [ 'en' => "در در خواست خطا وجود دارد",  'fa' => "در در خواست خطا وجود دارد",],
            '-80' => [ 'en' => "تراکنش مورد نظر یافت نشد",  'fa' => "تراکنش مورد نظر یافت نشد",],
            '-81' => [ 'en' => "خطای داخلی بانک",  'fa' => "خطای داخلی بانک",],
            '-90' => [ 'en' => "تراکنش قبلا تایید شده است",  'fa' => "تراکنش قبلا تایید شده است",],
            '120' => [ 'en' => "موجودی کافی نیست",  'fa' => "موجودی کافی نیست",],
            '130' => [ 'en' => "اطلاعات کارت اشتباه است",  'fa' => "اطلاعات کارت اشتباه است",],
            '131' => [ 'en' => "اطلاعات کارت اشتباه است",  'fa' => "اطلاعات کارت اشتباه است",],
            '160' => [ 'en' => "اطلاعات کارت اشتباه است",  'fa' => "اطلاعات کارت اشتباه است",],
            '132' => [ 'en' => "کارت مسدود یا منقضی می باشد",  'fa' => "کارت مسدود یا منقضی می باشد",],
            '133' => [ 'en' => "کارت مسدود یا منقضی می باشد",  'fa' => "کارت مسدود یا منقضی می باشد",],
            '140' => [ 'en' => "زمان مورد نظر به پایان رسیده است",  'fa' => "زمان مورد نظر به پایان رسیده است",],
            '200' => [ 'en' => "مبلغ بیش از سقف مجاز",  'fa' => "مبلغ بیش از سقف مجاز",],
            '201' => [ 'en' => "مبلغ بیش از سقف مجاز",  'fa' => "مبلغ بیش از سقف مجاز",],
            '202' => [ 'en' => "مبلغ بیش از سقف مجاز",  'fa' => "مبلغ بیش از سقف مجاز",],
            '166' => [ 'en' => "بانک صادر کننده مجوز انجام  تراکنش را صادر نکرده",'fa' => "بانک صادر کننده مجوز انجام  تراکنش را صادر نکرده",],
        ];

        if(isset($T_msg[$_status]))
        {
            if(\lib\define::get_language() === 'fa')
            {
                if(isset($T_msg[$_status]['fa']))
                {
                    return $T_msg[$_status]['fa'];
                }
            }

            if(isset($T_msg[$_status]['en']))
            {
                return $T_msg[$_status]['en'];
            }

            return T_("Unkown payment error");
        }
        else
        {
            return T_("Unkown payment error");
        }
        return $msg;
    }
}
?>