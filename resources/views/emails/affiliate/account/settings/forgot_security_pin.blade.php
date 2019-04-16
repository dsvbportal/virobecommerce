@extends('emails.affiliate.maillayout')
@section('content')
<tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$full_name}},</td>
</tr>
<tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - {{$uname}},</td>
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
<tr>
	<td align="center" style="font-size:30px;color:#000">Security PIN Reset Notification!</td>
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
<tr>
    <td style="font-size:14px;text-align:center"><p>Hey, {{$full_name}}! </p>
    <p>Did you forget your Security PIN? That's okay, you can reset your Security PIN using following Code. </p>
	<p>OTP code is: <br /><strong style="font-size:25px; display:inline-block; color: #009900">{{$code}}</strong></p>	    
    <p>If you did not request a Security PIN reset, please contact Customer Support.</p>
    <p>If you have any questions or need assistance, please e-mail us at   </p>
    </td>
</tr>
@stop