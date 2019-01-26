<?php
namespace dash\utility\pay\api\parsian;


class go
{

    public static function bank()
    {
        if(!\dash\option::config('parsian', 'status'))
        {
            \dash\log::set('pay:parsian:status:false');
            \dash\notif::error(T_("The parsian payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('parsian', 'LoginAccount'))
        {
            \dash\log::set('pay:parsian:LoginAccount:not:set');
            \dash\notif::error(T_("The parsian payment LoginAccount not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        $parsian = [];

        $parsian['LoginAccount'] = \dash\option::config('parsian', 'LoginAccount');

        if(\dash\option::config('parsian', 'CallBackUrl'))
        {
            $parsian['CallBackUrl'] = \dash\option::config('parsian', 'CallBackUrl');
        }
        else
        {
            $parsian['CallBackUrl'] = \dash\utility\pay\setting::get_callbck_url('parsian');
        }


        //START TRANSACTION BY CONDITION REQUEST
        $transaction_id = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            return \dash\utility\pay\setting::turn_back();
        }

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $parsian['Amount'] = floatval(\dash\utility\pay\setting::get_plus()) * 10;

        $parsian['OrderId'] = $transaction_id;

        $redirect = \dash\utility\pay\api\parsian\bank::pay($parsian);

        if($redirect)
        {
            $payment_response = \dash\utility\pay\api\parsian\bank::$payment_response;
            \dash\utility\pay\setting::set_payment_response1($payment_response);

            $Token = null;
            if(isset($payment_response->SalePaymentRequestResult->Token))
            {
                $Token = $payment_response->SalePaymentRequestResult->Token;
            }

            if($Token)
            {
                \dash\utility\pay\setting::set_condition('redirect');
                \dash\utility\pay\setting::set_banktoken($Token);

                \dash\utility\pay\setting::save();

                // redirect
                \dash\redirect::to($redirect);
                return;
            }
            else
            {
                \dash\log::set('pay:parsian:Token:not:set');
                \dash\notif::error(T_("The parsian payment Token not set"));
                return \dash\utility\pay\setting::turn_back();
            }
        }
        else
        {
            \dash\utility\pay\setting::save();
            // \dash\utility\pay\setting::turn_back();
            return false;
        }
    }

}
?>
