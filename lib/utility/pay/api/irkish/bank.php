<?php
namespace dash\utility\pay\api\irkish;


class bank
{

    public static $payment_response = [];

    /**
     * pay price
     *
     * @param      array  $_args  The arguments
     */
    public static function pay($_args = [])
    {

        // if soap is not exist return false
        if(!class_exists("soapclient"))
        {
            \dash\log::set('payment:irkish:soapclient:not:install');
            \dash\notif::error(T_("Can not connect to irkish gateway. Install it!"));
            return false;
        }

        try
        {
            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
                'exceptions'   => true,

            ];

            $client = @new \SoapClient('https://ikc.shaparak.ir/XToken/Tokens.xml', $soap_meta);

            $result = $client->__soapCall("MakeToken", array($_args));

            self::$payment_response = (array) $result;

            if(isset($result->MakeTokenResult->result) && $result->MakeTokenResult->result === true && isset($result->MakeTokenResult->token))
            {
                $token = $result->MakeTokenResult->token;
                \dash\log::set('payment:irkish:redirect');
                return $token;
            }
            else
            {
                \dash\log::set('payment:irkish:error');
                \dash\notif::error(T_("Error in connecting to bank service"));
                return false;
            }
        }
        catch (\Exception $e)
        {
            \dash\log::set('payment:irkish:error:load:web:services');
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
        try
        {
            $amount = $_args['amount'];
            unset($_args['amount']);

            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
                'exceptions'   => true,
                // 'cache_wsdl'   => WSDL_CACHE_NONE ,
                // 'encoding'     => 'UTF-8',
            ];

            $client = @new \SoapClient('https://ikc.shaparak.ir/XVerify/Verify.xml', $soap_meta);

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
        catch(\Exception $e)
        {
            \dash\log::set('payment:irkish:error:load:web:services:verify');
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
            if(\dash\language::current() === 'fa')
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