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


            $params = ['stream_context' => stream_context_create($options), 'exceptions' => true];
            $client = @new \SoapClient("https://services.asanpardakht.net/paygate/merchantservices.asmx?WSDL", $params);

            $result_param =
            [
                'merchantConfigurationID' => \dash\option::config('asanpardakht', 'MerchantConfigID'),
                'encryptedRequest'        => $encryptedRequest,
            ];

            $result = $client->RequestOperation($result_param);

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

            $result = $client->RequestVerification($params_result);

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

            $result = $client->EncryptInAES($params);

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

        $result = $client->DecryptInAES($params);
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
            case 301: $msg  = "پيكربندي پذيرنده اينترنتي نامعتبر است"; break;
            case 302: $msg  = "كليدهاي رمزنگاري نامعتبر هستند"; break;
            case 303: $msg  = "رمزنگاري نامعتبر است"; break;
            case 304: $msg  = "تعداد عناصر درخواست نامعتبر است"; break;
            case 305: $msg  = "نام كاربري يا رمز عبور پذيرنده نامعتبر است"; break;
            case 306: $msg  = "با آسان پرداخت تماس بگيريد"; break;
            case 307: $msg  = "سرور پذيرنده نامعتبر است"; break;
            case 308: $msg  = "شناسه فاكتور مي بايست صرفا عدد باشد"; break;
            case 309: $msg  = "مبلغ فاكتور نادرست ارسال شده است"; break;
            case 310: $msg  = "طول فيلد تاريخ و زمان نامعتبر است"; break;
            case 311: $msg  = "فرمت تاريخ و زمان ارسالي پذيرنده نامعتبر است"; break;
            case 312: $msg  = "نوع سرويس نامعتبر است"; break;
            case 313: $msg  = "شناسه پرداخت كننده نامعتبر است"; break;
            case 315: $msg  = "فرمت توصيف شيوه تسهيم شبا نامعتبر است"; break;
            case 316: $msg  = "شيوه تقسيم وجوه با مبلغ كل تراكنش همخواني ندارد"; break;
            case 317: $msg  = "شبا متعلق به پذيرنده نيست"; break;
            case 318: $msg  = "هيچ شبايي براي پذيرنده موجود نيست"; break;
            case 319: $msg  = "خطاي داخلي. دوباره درخواست ارسال شود"; break;
            case 320: $msg  = "شباي تكراري در رشته درخواست ارسال شده است"; break;
            case -100: $msg = "تاريخ ارسالي محلي پذيرنده نامعتبر است"; break;
            case -103: $msg = "مبلغ فاكتور براي پيكربندي فعلي پذيرنده معتبر نمي باشد"; break;
            case -106: $msg = "سرويس وجود ندارد يا براي پذيرنده فعال نيست"; break;
            case -109: $msg = "هيچ آدرس كال بكي براي درخواست پيكربندي نشده است"; break;
            case -112: $msg = "شماره فاكتور نامعتبر يا تكراري است"; break;
            case -115: $msg = "پذيرنده فعال نيست يا پيكربندي پذيرنده غيرمعتبر است"; break;
            case 500: $msg  = "بازبيني تراكنش با موفقيت انجام شد"; break;
            case 501: $msg  = "پردازش هنوز انجام نشده است"; break;
            case 502: $msg  = "وضعيت تراكنش نامشخص است"; break;
            case 503: $msg  = "تراكنش اصلي ناموفق بوده است"; break;
            case 504: $msg  = "قبلا درخواست بازبيني براي اين تراكنش داده شده است"; break;
            case 505: $msg  = "قبلا درخواست تسويه براي اين تراكنش ارسال شده است"; break;
            case 506: $msg  = "قبلا درخواست بازگشت براي اين تراكنش ارسال شده است"; break;
            case 507: $msg  = "تراكنش در ليست تسويه قرار دارد"; break;
            case 508: $msg  = "تراكنش در ليست بازگشت قرار دارد"; break;
            case 509: $msg  = "امكان انجام عمليات به سبب وجود مشكل داخلي وجود ندارد"; break;
            case 510: $msg  = "هويت درخواست كننده عمليات نامعتبر است"; break;
            case 600: $msg  = "درخواست تسويه تراكنش با موفقيت ارسال شد"; break;
            case 601: $msg  = "پردازش هنوز انجام نشده است"; break;
            case 602: $msg  = "وضعيت تراكنش نامشخص است"; break;
            case 603: $msg  = "تراكنش اصلي ناموفق بوده است"; break;
            case 604: $msg  = "تراكنش بازبيني نشده است"; break;
            case 605: $msg  = "قبلا درخواست بازگشت براي اين تراكنش ارسال شده است"; break;
            case 606: $msg  = "قبلا درخواست تسويه براي اين تراكنش ارسال شده است"; break;
            case 607: $msg  = "امكان انجام عمليات به سبب وجود مشكل داخلي وجود ندارد"; break;
            case 608: $msg  = "تراكنش در ليست منتظر بازگشت ها وجود دارد"; break;
            case 609: $msg  = "تراكنش در ليست منتظر تسويه ها وجود دارد"; break;
            case 610: $msg  = "هويت درخواست كننده عمليات نامعتبر است"; break;
            case 700: $msg  = "درخواست بازگشت تراكنش با موفقيت ارسال شد"; break;
            case 701: $msg  = "پردازش هنوز انجام نشده است"; break;
            case 702: $msg  = "وضعيت تراكنش نامشخص است"; break;
            case 703: $msg  = "تراكنش اصلي ناموفق بوده است"; break;
            case 704: $msg  = "امكان بازگشت يك تراكنش بازبيني شده وجود ندارد"; break;
            case 705: $msg  = "قبلا درخواست بازگشت تراكنش براي اين تراكنش ارسال شده است"; break;
            case 706: $msg  = "قبلا درخواست تسويه براي اين تراكنش ارسال شده است"; break;
            case 707: $msg  = "امكان انجام عمليات به سبب وجود مشكل داخلي وجود ندارد"; break;
            case 708: $msg  = "تراكنش در ليست منتظر بازگشت ها وجود دارد"; break;
            case 709: $msg  = "تراكنش در ليست منتظر تسويه ها وجود دارد"; break;
            case 710: $msg  = "هويت درخواست كننده عمليات نامعتبر است"; break;
            case 400: $msg  = "موفق"; break;
            case 401: $msg  = "حالت اوليه مقدار اوليه در شرايط"; break;
            case 402: $msg  = "هويت درخواست كننده نامعتبر است"; break;
            case 403: $msg  = "تراكنشي يافت نشد"; break;
            case 404: $msg  = "خطا در پردازش"; break;
            case 1100: $msg = "موفق"; break;
            case 1101: $msg = "هويت درخواست كننده نامعتبر است"; break;
            case 1102: $msg = "خطا در پردازش"; break;
            case 1103: $msg = "تراكنشي يافت نشد"; break;
            case 0: $msg    = "تراكنش با موفقيت انجام شد"; break;
            case 1: $msg    = "صادركننده كارت از انجام تراكنش صرف نظر كرد."; break;
            case 2: $msg    = "عمليات تاييديه اين تراكنش قبلاً با موفقيت صورت پذيرفته است."; break;
            case 3: $msg    = "پذيرنده فروشگاهي نامعتبر مي باشد"; break;
            case 4: $msg    = "كارت توسط دستگاه ضبط شود."; break;
            case 5: $msg    = "به تراكنش رسيدگي نشد."; break;
            case 6: $msg    = "بروز خطا."; break;
            case 7: $msg    = "به دليل شرايط خاص كارت توسط دستگاه ضبط شود"; break;
            case 8: $msg    = "با تشخيص هويت دارنده ي كارت، تراكنش موفق مي باشد."; break;
        }

        return $msg;

    }

}
?>