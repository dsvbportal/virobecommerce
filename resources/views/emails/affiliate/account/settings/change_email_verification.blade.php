@extends('emails.affiliate.maillayout')
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
    <td bgcolor="#d7d7d7" height="1"></td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
<tr> <td style="font-size:14px;color:#929292">
		<p>We received a request to change your email address. If you requested this change, please click the link below and paste the email code in the form of to continue.</p>		    
        <p>Your email verification code is: {{$vcode}}</p>
        <p style="text-align: center;">
            <a target="_blank" style="background:none repeat scroll 0 0 #00cf00;font-weight:bold;color:#fff;font-size:14px;text-decoration:none;min-height:40px;line-height:40px;padding:0px 20px;text-transform:uppercase;display:inline-block" href="<?php echo $email_verify_link;?>">I Agree</a>
        </p>		
        <p>(OR) Copy and paste the below link in your browser.<br/><?php echo $email_verify_link;?></p>
        <p>If you did not request for Change Email, Please contact Customer Support.</p>       
    </td>
</tr>
@stop