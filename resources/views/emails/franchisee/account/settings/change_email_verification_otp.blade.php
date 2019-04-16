@extends('emails.franchisee.maillayout')
@section('content')
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$userSess->full_name}},</td>
</tr>
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - {{$userSess->uname}} </td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>

<tr>
    <td align="center" style="font-size:30px;color:#000">Email Verification OTP</td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>

<tr> <td style="font-size:14px;color:#929292">
		<p>We received a request to update this email address to your account.</p>		    
        <p>In order to process this request, Please use your one time verification code to approve this request:</p>
        <p style="text-align: center;">
            <span style="background:none repeat scroll 0 0 #00cf00;font-weight:bold;color:#fff;font-size:14px;text-decoration:none;min-height:40px;line-height:40px;padding:0px 20px;text-transform:uppercase;display:inline-block">{{$code}}</span>
        </p>
        <p>If you did not request for Change Email, Please contact our Customer Support.</p>       
    </td>
</tr>
@stop