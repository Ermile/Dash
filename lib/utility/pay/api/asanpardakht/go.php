<?php
namespace dash\utility\pay\api\asanpardakht;


class go
{

    public static function bank()
    {
        if(!\dash\option::config('asanpardakht', 'status'))
        {
            \dash\log::set('pay:asanpardakht:status:false');
            \dash\notif::error(T_("The asanpardakht payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('asanpardakht', 'MerchantID'))
        {
            \dash\log::set('pay:asanpardakht:MerchantID:false');
            \dash\notif::error(T_("The asanpardakht payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        $username = \dash\option::config('asanpardakht', 'Username');
        $password = \dash\option::config('asanpardakht', 'Password');

        $asanpardakht = [];

        if(\dash\option::config('asanpardakht', 'CallBackUrl'))
        {
            $asanpardakht['CallBackUrl'] = \dash\option::config('asanpardakht', 'CallBackUrl');
        }
        else
        {
            $asanpardakht['CallBackUrl'] = \dash\utility\pay\setting::get_callbck_url('asanpardakht');
        }

        //START TRANSACTION BY CONDITION REQUEST
        $transaction_id = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            return \dash\utility\pay\setting::turn_back();
        }

        $price = \dash\utility\pay\setting::get_plus();
        $price = floatval($price) * 10;

        $orderId        = $transaction_id;
        $localDate      = date("Ymd His");
        $additionalData = "";
        $callBackUrl    = $asanpardakht['CallBackUrl'];
        $req            = "1,{$username},{$password},{$orderId},{$price},{$localDate},{$additionalData},{$callBackUrl},0";

        $asanpardakht_args =
        [
            'orderId'        => $orderId,
            'localDate'      => $localDate,
            'additionalData' => $additionalData,
            'callBackUrl'    => $callBackUrl,
            'req'            => $req,
        ];

        $RefId = \dash\utility\pay\api\asanpardakht\bank::pay($asanpardakht_args);

        \dash\utility\pay\setting::set_payment_response1(\dash\utility\pay\api\asanpardakht\bank::$payment_response);

        if($RefId)
        {
            \dash\utility\pay\setting::set_condition('redirect');
            \dash\utility\pay\setting::set_banktoken($RefId);
            \dash\utility\pay\setting::save();

            // redirect to enter/redirect
            \dash\session::set('redirect_page_url', 'https://asan.shaparak.ir/');
            \dash\session::set('redirect_page_method', 'post');
            \dash\session::set('redirect_page_args', ['RefId' => $RefId]);
            \dash\session::set('redirect_page_title', T_("Redirect to asanpardakht payment"));
            \dash\session::set('redirect_page_button', T_("Redirect"));
            \dash\notif::direct();
            \dash\redirect::to(\dash\utility\pay\setting::get_callbck_url('redirect_page'));
            return true;

        }
        else
        {
            \dash\utility\pay\setting::save();
            // return \dash\utility\pay\setting::turn_back();
            return false;
        }
    }
}
?>