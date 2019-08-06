<?php
namespace dash\utility\pay\api\asanpardakht;


class bank
{

    public static $payment_response = [];

    public static $KEY             = null;
    public static $IV              = null;


    public static function set_key_iv()
    {
        self::$KEY = \dash\option::config('asanpardakht', 'EncryptionKey');
        self::$IV  = \dash\option::config('asanpardakht', 'EncryptionVector');
    }


    /**
     * pay price
     *
     * @param      array  $_args  The arguments
     */
    public static function pay($_args = [])
    {
        self::set_key_iv();

        // if soap is not exist return false
        if(!class_exists("soapclient"))
        {
            \dash\log::set('payment:asanpardakht:soapclient:not:install');
            \dash\notif::error(T_("Can not connect to asanpardakht gateway. Install it!"));
            return false;
        }

        $encryptedRequest = self::encrypt($_args['req']);

        try
        {
            $options =
            [
                'ssl' =>
                [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ]
            ];


            $params = ['stream_context' => stream_context_create($options), 'exceptions' => true, 'keep_alive' => false];
            $client = @new \SoapClient("https://services.asanpardakht.net/paygate/merchantservices.asmx?WSDL", $params);

            $result_param =
            [
                'merchantConfigurationID' => \dash\option::config('asanpardakht', 'MerchantConfigID'),
                'encryptedRequest'        => $encryptedRequest,
            ];

            $result = @$client->RequestOperation($result_param);

            if(isset($result->RequestOperationResult))
            {
                $result = $result->RequestOperationResult;

                if ($result{0} == '0')
                {
                    $token = substr($result,2);
                    return $token;
                }
                else
                {
                    \dash\log::set('payment:asanpardakht:error1');
                    \dash\notif::error(self::msg($result));
                    return false;
                }

            }
            else
            {
                \dash\log::set('payment:asanpardakht:error2');
                \dash\notif::error(T_("Error in payment (have not result)"));
                return false;
            }
        }
        catch (\Exception $E)
        {
            \dash\log::set('payment:asanpardakht:error:load:web:services');
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
        self::set_key_iv();

        $RetArr             = $_args;
        $Amount             = isset($RetArr[0]) ? $RetArr[0] : null;
        $SaleOrderId        = isset($RetArr[1]) ? $RetArr[1] : null;
        $RefId              = isset($RetArr[2]) ? $RetArr[2] : null;
        $ResCode            = isset($RetArr[3]) ? $RetArr[3] : null;
        $ResMessage         = isset($RetArr[4]) ? $RetArr[4] : null;
        $PayGateTranID      = isset($RetArr[5]) ? $RetArr[5] : null;
        $RRN                = isset($RetArr[6]) ? $RetArr[6] : null;
        $LastFourDigitOfPAN = isset($RetArr[7]) ? $RetArr[7] : null;

        try
        {
            $options =
            [
                'ssl' =>
                [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ]
            ];

            $params = ['stream_context' => stream_context_create($options), 'exceptions'   => true,];
            $client = @new \SoapClient("https://services.asanpardakht.net/paygate/merchantservices.asmx?WSDL", $params);

            $username = \dash\option::config('asanpardakht', 'Username');
            $password = \dash\option::config('asanpardakht', 'Password');

            $encryptedCredintials = self::encrypt("{$username},{$password}");

            $params_result =
            [
                'merchantConfigurationID' => \dash\option::config('asanpardakht', 'MerchantConfigID'),
                'encryptedCredentials'    => $encryptedCredintials,
                'payGateTranID'           => $PayGateTranID,
            ];

            $result = @$client->RequestVerification($params_result);

            if(isset($result->RequestVerificationResult))
            {
                $result = $result->RequestVerificationResult;

                if ($result != '500')
                {
                    return false;
                }
                else
                {
                    return true;
                }
            }
            else
            {
                return false;
            }
        }
        catch (\Exception $E)
        {
            return false;
        }
    }



    public static function encrypt($string = "")
    {
        $KEY = self::$KEY;
        $IV = self::$IV;

        if (PHP_MAJOR_VERSION <= 5)
        {
            $key = base64_decode($KEY);
            $iv  = base64_decode($IV);
            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, self::addpadding($string), MCRYPT_MODE_CBC, $iv));
        }
        else
        {
            return self::EncryptWS($string);
        }
    }


    public static function EncryptWS($string = "")
    {
        $KEY = self::$KEY;
        $IV = self::$IV;

        try
        {
            $options =
            [
                'ssl' =>
                [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ]
            ];


            $params = ['stream_context' => stream_context_create($options), 'exceptions'   => true,];

            $client = @new \SoapClient("https://services.asanpardakht.net/paygate/internalutils.asmx?WSDL", $params);

            $params =
            [
                'aesKey'        => $KEY,
                'aesVector'     => $IV,
                'toBeEncrypted' => $string,
            ];

            $result = @$client->EncryptInAES($params);

            if(isset($result->EncryptInAESResult))
            {
                return $result->EncryptInAESResult;
            }
            return false;
        }
        catch (\Exception $E)
        {
            return false;
        }
    }

    public static function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }


    public static function strippadding($string)
    {
        $slast  = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);

        if(preg_match("/$slastc{".$slast."}/", $string))
        {
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        }
        else
        {
            return false;
        }
    }


    public static function decrypt($string = "")
    {
        $KEY = self::$KEY;
        $IV = self::$IV;

        if(PHP_MAJOR_VERSION <= 5)
        {
            $key    = base64_decode($KEY);
            $iv     = base64_decode($IV);
            $string = base64_decode($string);
            return self::strippadding(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $iv));
        }
        else
        {
            return self::DecryptWS($string);
        }
    }


    public static function DecryptWS($string = "")
    {
        $KEY = self::$KEY;
        $IV = self::$IV;

        try
        {
            $options =
            [
                'ssl' =>
                [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ]
            ];

            $params = ['stream_context' => stream_context_create($options), 'exceptions'   => true,];

            $client = @new \SoapClient("https://services.asanpardakht.net/paygate/internalutils.asmx?WSDL", $params);
        }
        catch (\Exception $E)
        {
            return false;
        }

        $params =
        [
            'aesKey'        => $KEY,
            'aesVector'     => $IV,
            'toBeDecrypted' => $string
        ];

        $result = @$client->DecryptInAES($params);
        if(isset($result->DecryptInAESResult))
        {
            return $result->DecryptInAESResult;
        }
        return false;
    }

    public static function msg($_code)
    {
        if(!is_numeric($_code))
        {
            return T_("Error");
        }

        $msg = T_("Error in payment code :result", ['result' => (string) $_code]);

        if(\dash\language::current() !== 'fa')
        {
            return $msg;
        }

        switch (intval($_code))
        {
            case 301: $msg  = "پیكربندی پذیرنده اینترنتی نامعتبر است"; break;
            case 302: $msg  = "كلیدهای رمزنگاری نامعتبر هستند"; break;
            case 303: $msg  = "رمزنگاری نامعتبر است"; break;
            case 304: $msg  = "تعداد عناصر درخواست نامعتبر است"; break;
            case 305: $msg  = "نام كاربری یا رمز عبور پذیرنده نامعتبر است"; break;
            case 306: $msg  = "با آسان پرداخت تماس بگیرید"; break;
            case 307: $msg  = "سرور پذیرنده نامعتبر است"; break;
            case 308: $msg  = "شناسه فاكتور می بایست صرفا عدد باشد"; break;
            case 309: $msg  = "مبلغ فاكتور نادرست ارسال شده است"; break;
            case 310: $msg  = "طول فیلد تاریخ و زمان نامعتبر است"; break;
            case 311: $msg  = "فرمت تاریخ و زمان ارسالی پذیرنده نامعتبر است"; break;
            case 312: $msg  = "نوع سرویس نامعتبر است"; break;
            case 313: $msg  = "شناسه پرداخت كننده نامعتبر است"; break;
            case 315: $msg  = "فرمت توصیف شیوه تسهیم شبا نامعتبر است"; break;
            case 316: $msg  = "شیوه تقسیم وجوه با مبلغ كل تراكنش همخوانی ندارد"; break;
            case 317: $msg  = "شبا متعلق به پذیرنده نیست"; break;
            case 318: $msg  = "هیچ شبایی برای پذیرنده موجود نیست"; break;
            case 319: $msg  = "خطای داخلی. دوباره درخواست ارسال شود"; break;
            case 320: $msg  = "شبای تكراری در رشته درخواست ارسال شده است"; break;
            case -100: $msg = "تاریخ ارسالی محلی پذیرنده نامعتبر است"; break;
            case -103: $msg = "مبلغ فاكتور برای پیكربندی فعلی پذیرنده معتبر نمی باشد"; break;
            case -106: $msg = "سرویس وجود ندارد یا برای پذیرنده فعال نیست"; break;
            case -109: $msg = "هیچ آدرس كال بكی برای درخواست پیكربندی نشده است"; break;
            case -112: $msg = "شماره فاكتور نامعتبر یا تكراری است"; break;
            case -115: $msg = "پذیرنده فعال نیست یا پیكربندی پذیرنده غیرمعتبر است"; break;
            case 500: $msg  = "بازبینی تراكنش با موفقیت انجام شد"; break;
            case 501: $msg  = "پردازش هنوز انجام نشده است"; break;
            case 502: $msg  = "وضعیت تراكنش نامشخص است"; break;
            case 503: $msg  = "تراكنش اصلی ناموفق بوده است"; break;
            case 504: $msg  = "قبلا درخواست بازبینی برای این تراكنش داده شده است"; break;
            case 505: $msg  = "قبلا درخواست تسویه برای این تراكنش ارسال شده است"; break;
            case 506: $msg  = "قبلا درخواست بازگشت برای این تراكنش ارسال شده است"; break;
            case 507: $msg  = "تراكنش در لیست تسویه قرار دارد"; break;
            case 508: $msg  = "تراكنش در لیست بازگشت قرار دارد"; break;
            case 509: $msg  = "امكان انجام عملیات به سبب وجود مشكل داخلی وجود ندارد"; break;
            case 510: $msg  = "هویت درخواست كننده عملیات نامعتبر است"; break;
            case 600: $msg  = "درخواست تسویه تراكنش با موفقیت ارسال شد"; break;
            case 601: $msg  = "پردازش هنوز انجام نشده است"; break;
            case 602: $msg  = "وضعیت تراكنش نامشخص است"; break;
            case 603: $msg  = "تراكنش اصلی ناموفق بوده است"; break;
            case 604: $msg  = "تراكنش بازبینی نشده است"; break;
            case 605: $msg  = "قبلا درخواست بازگشت برای این تراكنش ارسال شده است"; break;
            case 606: $msg  = "قبلا درخواست تسویه برای این تراكنش ارسال شده است"; break;
            case 607: $msg  = "امكان انجام عملیات به سبب وجود مشكل داخلی وجود ندارد"; break;
            case 608: $msg  = "تراكنش در لیست منتظر بازگشت ها وجود دارد"; break;
            case 609: $msg  = "تراكنش در لیست منتظر تسویه ها وجود دارد"; break;
            case 610: $msg  = "هویت درخواست كننده عملیات نامعتبر است"; break;
            case 700: $msg  = "درخواست بازگشت تراكنش با موفقیت ارسال شد"; break;
            case 701: $msg  = "پردازش هنوز انجام نشده است"; break;
            case 702: $msg  = "وضعیت تراكنش نامشخص است"; break;
            case 703: $msg  = "تراكنش اصلی ناموفق بوده است"; break;
            case 704: $msg  = "امكان بازگشت یك تراكنش بازبینی شده وجود ندارد"; break;
            case 705: $msg  = "قبلا درخواست بازگشت تراكنش برای این تراكنش ارسال شده است"; break;
            case 706: $msg  = "قبلا درخواست تسویه برای این تراكنش ارسال شده است"; break;
            case 707: $msg  = "امكان انجام عملیات به سبب وجود مشكل داخلی وجود ندارد"; break;
            case 708: $msg  = "تراكنش در لیست منتظر بازگشت ها وجود دارد"; break;
            case 709: $msg  = "تراكنش در لیست منتظر تسویه ها وجود دارد"; break;
            case 710: $msg  = "هویت درخواست كننده عملیات نامعتبر است"; break;
            case 400: $msg  = "موفق"; break;
            case 401: $msg  = "حالت اولیه مقدار اولیه در شرایط"; break;
            case 402: $msg  = "هویت درخواست كننده نامعتبر است"; break;
            case 403: $msg  = "تراكنشی یافت نشد"; break;
            case 404: $msg  = "خطا در پردازش"; break;
            case 1100: $msg = "موفق"; break;
            case 1101: $msg = "هویت درخواست كننده نامعتبر است"; break;
            case 1102: $msg = "خطا در پردازش"; break;
            case 1103: $msg = "تراكنشی یافت نشد"; break;
            case 0: $msg    = "تراكنش با موفقیت انجام شد"; break;
            case 1: $msg    = "صادركننده كارت از انجام تراكنش صرف نظر كرد."; break;
            case 2: $msg    = "عملیات تاییدیه این تراكنش قبلاً با موفقیت صورت پذیرفته است."; break;
            case 3: $msg    = "پذیرنده فروشگاهی نامعتبر می باشد"; break;
            case 4: $msg    = "كارت توسط دستگاه ضبط شود."; break;
            case 5: $msg    = "به تراكنش رسیدگی نشد."; break;
            case 6: $msg    = "بروز خطا."; break;
            case 7: $msg    = "به دلیل شرایط خاص كارت توسط دستگاه ضبط شود"; break;
            case 8: $msg    = "با تشخیص هویت دارنده ی كارت، تراكنش موفق می باشد."; break;
        }

        return $msg;

    }

}
?>