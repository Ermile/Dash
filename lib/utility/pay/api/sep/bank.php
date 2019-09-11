<?php
namespace dash\utility\pay\api\sep;


class bank
{

    public static $payment_response = [];


    public static function pay($_args = [])
    {

        // if soap is not exist return false
        if(!class_exists("soapclient"))
        {
            \dash\log::set('payment:sep:soapclient:not:install');
            \dash\notif::error(T_("Can not connect to sep gateway. Install it!"));
            return false;
        }

        try
        {
            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
                'exceptions'   => true,
            ];

            $client = @new \SoapClient('https://sep.shaparak.ir/payments/initpayment.asmx?wsdl', $soap_meta);
            //RequestToken(MID, ResNum, Amount,RedirectURL,SegAmount1, SegAmount2,SegAmount3, SegAmount4, SegAmount5, SegAmount6, AdditionalData1,AdditionalData2, Wage)
            $result = $client->RequestToken($_args['MID'], $_args['ResNum'], $_args['Amount'], $_args['RedirectURL']);

            self::$payment_response = $result;

            if(intval($result) < 0)
            {
                \dash\notif::error(self::msg($result));
                return false;
            }
            else
            {
               return $result;
            }

        }
        catch (\Exception $e)
        {
            \dash\log::set('payment:sep:error:load:web:services');
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

            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
                'exceptions'   => true,
            ];

            $client    = @new \SoapClient('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL', $soap_meta);

            $result = $client->VerifyTransaction($_args['RefNum'], $_args['MID']);

            self::$payment_response = $result;

            if (intval($result) === intval($_args['Amount']))
            {
               return true;
            }
            else
            {
                \dash\notif::error(self::msg($result), $result);
                return false;
            }
        }
        catch(\Exception $e)
        {
            \dash\log::set('payment:sep:error:load:web:services:verify');
            \dash\notif::error(T_("Error in load web services"));
            return false;
        }
    }


   /**
     * { function_description }
     *
     * @param      array  $_args  The arguments
     */
    public static function reverse($_args = [])
    {
        try
        {

            $soap_meta =
            [
                'soap_version' => 'SOAP_1_1',
                'exceptions'   => true,
            ];

            $client    = @new \SoapClient('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL', $soap_meta);

            $result = $client->reverseTransaction($_args['RefNum'], $_args['MID'], $_args['Username'], $_args['Password']);

            self::$payment_response = $result;

            if (intval($result) === 1)
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
            \dash\log::set('payment:sep:error:load:web:services:verify');
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
        $msg[-1]  = "خطای در پردازش اطالعات ارسالی";
        $msg[-3]  = "ورودی ها حاوی کارکترهای غیرمجاز میباشند.";
        $msg[-4]  = "کلمه عبور یا کد فروشنده اشتباه است";
        $msg[-6]  = "سند قبلا برگشت کامل یافته است. یا خارج از زمان 30 دقیقه ارسال شده است";
        $msg[-7]  = "رسید دیجیتالی تهی است";
        $msg[-8]  = "طول ورودی ها بیشتر از حد مجاز است";
        $msg[-9]  = "وجود کارکترهای غیرمجاز در مبلغ برگشتی";
        $msg[-10] = "رسید دیجیتالی به صورت بیس ۶۴ نیست";
        $msg[-11] = "طول ورودیها ک تر از حد مجاز است";
        $msg[-12] = "مبلغ برگشتی منفی است.";
        $msg[-13] = "مبلغ برگشتی برای برگشت جزئی بیش از مبلغ برگشت نخورده رسید دیجیتالی است.";
        $msg[-14] = "چنین تراکنشی تعریف نشده است.";
        $msg[-15] = "مبلغ برگشتی به صورت اعشاری داده شده است.";
        $msg[-16] = "خطای داخلی سیستم";
        $msg[-17] = "برگشت زدن جزیی تراکنش مجاز نمی باشد.";
        $msg[-18] = "شناسه آی‌پی اشتباه است";


        if(isset($msg[$_status]) && \dash\language::current() === 'fa')
        {
            return $msg[$_status];
        }
        else
        {
            return T_("Unkown payment error");
        }

    }
}
?>
