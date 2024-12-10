@extends('layouts.mail')

@section('content')
    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
           style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
        <!-- Body content -->
        <tr>
            <td class="content-cell"
                style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                    Hello!</h1>
                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    We noticed a login attempt to your account and an additional step is required to verify it’s you.</p>
                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    Please use the following authentication code:</p>
                <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0"
                       style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 30px auto; padding: 0; text-align: center; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                    <tr>
                        <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                   style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                <tr>
                                    <td align="center"
                                        style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                        <table border="0" cellpadding="0" cellspacing="0"
                                               style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                            <tr>
                                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                                    <strong>{{$code}}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    If you did not attempted to login, no further action is required.</p>
                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    Regards,<br>James Frew Team</p>
            </td>
        </tr>
    </table>
@endsection