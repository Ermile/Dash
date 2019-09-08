<?php
namespace dash\utility\pay\api\sep;


class go
{

    public static function bank()
    {
        if(!\dash\option::config('sep', 'status'))
        {
            \dash\log::set('pay:sep:status:false');
            \dash\notif::error(T_("The sep payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('sep', 'MID'))
        {
            \dash\log::set('pay:sep:MID:null');
            \dash\notif::error(T_("The sep payment MID not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        $RedirectURL        = \dash\utility\pay\setting::get_callbck_url('sep');


        $sep                = [];
        $sep['MID']         = \dash\option::config('sep', 'MID');
        $sep['RedirectURL'] = $RedirectURL;

        $amount = \dash\utility\pay\setting::get_plus();
        $amount = floatval($amount) * 10;

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $sep['Amount'] = (string) $amount;

        //START TRANSACTION BY CONDITION REQUEST
        $transaction_id = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            return \dash\utility\pay\setting::turn_back();
        }

        // set in this step and check in other step
        // $sep['specialPaymentId'] = $transaction_id;
        $sep['ResNum'] = $transaction_id;

        $Token = \dash\utility\pay\api\sep\bank::pay($sep);

        \dash\utility\pay\setting::set_payment_response1(\dash\utility\pay\api\sep\bank::$payment_response);

        if($Token)
        {
            \dash\utility\pay\setting::set_condition('redirect');

            \dash\utility\pay\setting::set_banktoken($Token);

            \dash\utility\pay\setting::save();

            // redirect to enter/redirect
            \dash\session::set('redirect_page_url', 'https://sep.shaparak.ir/payment.aspx');
            \dash\session::set('redirect_page_method', 'post');
            \dash\session::set('redirect_page_args', ['Token' => $Token, 'RedirectURL' => $RedirectURL]);
            \dash\session::set('redirect_page_title', T_("Redirect to sep payment"));
            \dash\session::set('redirect_page_button', T_("Redirect"));
            \dash\notif::direct();
            \dash\redirect::to(\dash\utility\pay\setting::get_callbck_url('redirect_page'));
            return true;
        }
        else
        {
            \dash\utility\pay\setting::save();
            //\dash\utility\pay\setting::turn_back();
            return false;
        }
    }
}
?>
