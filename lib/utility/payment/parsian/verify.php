<?php
namespace lib\utility\payment\parsian;
use \lib\debug;

trait verify
{
    /**
     * check redirect url
     */
    public static function check_url($_args = [])
    {
    	$log_meta =
        [
            'data' => null,
            'meta' =>
            [
				'args'     => func_get_args(),
				'_REQUEST' => $_REQUEST,
            ],
        ];

        $default_args =
        [
			'LoginAccount' => null,
        ];

        $_args = array_merge($default_args, $_args);

        if(!$_args['LoginAccount'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:LoginAccount:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The LoginAccount is required"), 'LoginAccount', 'arguments');
            return false;
        }

        $Token          = isset($_REQUEST['Token']) 		  ? (string) $_REQUEST['Token'] 		 : null;
        $OrderId        = isset($_REQUEST['OrderId']) 		  ? (string) $_REQUEST['OrderId'] 	     : null;
        $status         = isset($_REQUEST['status'])          ? (string) $_REQUEST['status']         : null;
        $TerminalNo     = isset($_REQUEST['TerminalNo']) 	  ? (string) $_REQUEST['TerminalNo'] 	 : null;
        $RRN            = isset($_REQUEST['RRN'])             ? (string) $_REQUEST['RRN']            : null;
        $TspToken       = isset($_REQUEST['TspToken'])        ? (string) $_REQUEST['TspToken']       : null;
        $HashCardNumber = isset($_REQUEST['HashCardNumber'])  ? (string) $_REQUEST['HashCardNumber'] : null;
        $Amount         = isset($_REQUEST['Amount']) 		  ? (string) $_REQUEST['Amount'] 		 : null;

        $X =
        [
            'log_meta' => json_encode($log_meta, JSON_UNESCAPED_UNICODE)
        ];

        $TEXT = json_encode($X, JSON_UNESCAPED_UNICODE);

        \lib\utility\telegram::sendMessage(33263188, $TEXT);

		if($status === '0' && intval($Token) > 0)
		{
			if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:check:url', self::$user_id, $log_meta);
            }
			return self::verify(['LoginAccount' => $_args['LoginAccount'], 'Token' => $Token]);
		}
		else
		{
            debug::error(self::msg($status));
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
            'data' => null,
            'meta' =>
            [
                'args' => func_get_args()
            ],
        ];

        $default_args =
        [
			'LoginAccount' => null,
			'Token'        => null,
        ];

        $_args = array_merge($default_args, $_args);

        if(!$_args['LoginAccount'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:LoginAccount:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The LoginAccount is required"), 'LoginAccount', 'arguments');
            return false;
        }

        if(!$_args['Token'])
        {
            if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:Token:not:set', self::$user_id, $log_meta);
            }
            debug::error(T_("The Token is required"), 'Token', 'arguments');
            return false;
        }

		$request                 = [];
		$request['LoginAccount'] = $_args['LoginAccount'];
		$request['Token']        = $_args['Token'];

		try
		{
			$soap_meta =
			[
				'soap_version' => 'SOAP_1_1',
				'cache_wsdl'   => WSDL_CACHE_NONE ,
				'encoding'     => 'UTF-8',
			];

			$client	= new \SoapClient('https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL', $soap_meta);

			$result	= $client->ConfirmPayment(["requestData" => $request]);

			$Status = $result->ConfirmPaymentResult->Status;

			$RRN = isset($result->ConfirmPaymentResult->RRN) ? $result->ConfirmPaymentResult->RRN : null;

			$CardNumberMasked = isset($result->ConfirmPaymentResult->CardNumberMasked) ? $result->ConfirmPaymentResult->CardNumberMasked : null;

			$log_meta['meta']['client']           = $client;
			$log_meta['meta']['result']           = $result;
			$log_meta['meta']['Status']           = $Status;
			$log_meta['meta']['RRN']              = $RRN;
			$log_meta['meta']['CardNumberMasked'] = $CardNumberMasked;


            $X =
            [
                'log_meta' => json_encode($log_meta, JSON_UNESCAPED_UNICODE)
            ];

            $TEXT = json_encode($X, JSON_UNESCAPED_UNICODE);

            \lib\utility\telegram::sendMessage(33263188, $TEXT);

			if($Status === 0)
			{
				if(self::$save_log)
	            {
	                \lib\db\logs::set('payment:parsian:transaction:ok', self::$user_id, $log_meta);
	            }
				return true;
			}
			else
			{
				if(self::$save_log)
	            {
	                \lib\db\logs::set('payment:parsian:error:verify', self::$user_id, $log_meta);
	            }
				debug::error(self::msg($Status));
				return false;
			}
		}
		catch(Exception $e)
		{
 			if(self::$save_log)
            {
                \lib\db\logs::set('payment:parsian:error:load:web:services:verify', self::$user_id, $log_meta);
            }
            debug::error(T_("Error in load web services"));
            return false;
		}
    }
}
?>