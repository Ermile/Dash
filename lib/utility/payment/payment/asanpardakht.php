<?php
namespace dash\utility\payment\payment;


class asanpardakht
{

	/**
     * auto save logs
     *
     * @var        boolean
     */
    public static $save_log         = false;
    // to save log for this user
    public static $user_id          = null;
    public static $log_data         = null;
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
        $log_meta =
        [
            'data' => self::$log_data,
            'meta' =>
            [
                'args' => func_get_args(),
            ],
        ];

        // if soap is not exist return false
        if(!class_exists("soapclient"))
        {
            if(self::$save_log)
            {
                \dash\db\logs::set('payment:asanpardakht:soapclient:not:install', self::$user_id, $log_meta);
            }
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
                    \dash\db\logs::set('payment:asanpardakht:redirect', self::$user_id, $log_meta);
                    $token = substr($result,2);
                    return $token;
                }
                else
                {
                    \dash\db\logs::set('payment:asanpardakht:error1', self::$user_id, $log_meta);
                    \dash\notif::error(T_("Error in payment code :result", ['result' => (string) $result]));
                    return false;
                }

            }
            else
            {
                \dash\db\logs::set('payment:asanpardakht:error2', self::$user_id, $log_meta);
                \dash\notif::error(T_("Error in payment (have not result)"));
                return false;
            }
        }
        catch (\Exception $E)
        {
            \dash\db\logs::set('payment:asanpardakht:error:load:web:services', self::$user_id, $log_meta);
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

}
?>