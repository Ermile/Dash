<?php
namespace lib\utility\payment\parsian;
use \lib\debug;

trait pay
{
	/**
     * auto save logs
     *
     * @var        boolean
     */
    public static $save_log = false;
    // to save log for this user
    public static $user_id  = null;

    /**
     * pay price
     *
     * @param      array  $_args  The arguments
     */
    public static function pay($_args = [])
    {
        $log_meta =
        [
            'data' => null,
            'meta' =>
            [
                'args' => func_get_args()
            ],
        ];

        $default_args =
        [
			'LoginAccount' => null,
			'Amount'       => null,
			'OrderId'      => null,
			'CallBackUrl'  => null,
        ];

        $_args = array_merge($default_args, $_args);
        // if soap is not exist return false
        if(!class_exists("soapclient"))
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:soapclient:not:install', self::$user_id, $log_meta);
            }
            debug::error(T_("Can not connect to parsian gateway. Install it!"));
            return false;
        }

        if(!$_args['LoginAccount'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:LoginAccount:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The LoginAccount is required"), 'LoginAccount', 'arguments');
            return false;
        }

        if(!$_args['Amount'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:amount:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The Amount is required"), 'Amount', 'arguments');
            return false;
        }

        if(!$_args['OrderId'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:description:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The OrderId is required"), 'OrderId', 'arguments');
            return false;
        }

        if(!$_args['CallBackUrl'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:CallBackUrl:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The CallBackUrl is required"), 'CallBackUrl', 'arguments');
            return false;
        }

        $price = $_args['Amount'];

        if(is_numeric($price) && $price > 0 && $price == round($price, 0))
        {
            // no thing!
        }
        else
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:amount:lessthan:0', self::$user_id, $log_meta);
            }
            debug::error(T_("Amount must be larger than 0"), 'Amount', 'arguments');
            return false;
        }

		$request      = [];
		$LoginAccount = $request['LoginAccount'] = $_args['LoginAccount'];
		$Amount       = $request['Amount']       = $_args['Amount'];
		$OrderId      = $request['OrderId']      = $_args['OrderId'];
		$CallBackUrl  = $request['CallBackUrl']  = $_args['CallBackUrl'];

        try
        {
        	$soap_meta =
        	[
				'soap_version' => 'SOAP_1_1',
				'cache_wsdl'   => WSDL_CACHE_NONE ,
				'encoding'     => 'UTF-8',
        	];

			$client = new \SoapClient('https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL',$soap_meta);

			$result = $client->SalePaymentRequest(["requestData" => $request]);

            $X =
            [
                'log_meta' => json_encode($log_meta, JSON_UNESCAPED_UNICODE),
                'client'   => json_encode((array) $result, JSON_UNESCAPED_UNICODE),
                'request'  => json_encode((array) $request, JSON_UNESCAPED_UNICODE)
            ];

            $TEXT = json_encode($X, JSON_UNESCAPED_UNICODE);

            \lib\utility\telegram::sendMessage(33263188, $TEXT);

			$status = $result->SalePaymentRequestResult->Status;
			$token  = $result->SalePaymentRequestResult->Token;
			$msg    = self::msg($status);

            if ($status === 0 && $token > 0)
            {
                if(self::$save_log)
                {
                    \lib\db\logs::set('payment:parsian:redirect', self::$user_id, $log_meta);
                }

				$url      = "https://pec.shaparak.ir/NewIPG/?Token=" . $token;
				$redirect = (new \lib\redirector($url, false))->redirect();
            }
            else
            {
                if(self::$save_log)
                {
                    \lib\db\logs::set('payment:parsian:error', self::$user_id, $log_meta);
                }
                debug::error($msg);
                return false;
            }
        }
        catch (SoapFault $e)
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:error:load:web:services', self::$user_id, $log_meta);
            }
            debug::error(T_("Error in load web services"));
            return false;
        }
    }
}
?>