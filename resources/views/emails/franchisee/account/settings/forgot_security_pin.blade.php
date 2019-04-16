@extends('emails.franchisee.maillayout')
@section('content')
<tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$full_name}},</td>
</tr>
<tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#000">Username  - {{$uname}},</td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>
<tr>
	 <td align="center" style="font-size:15px;color:#000;text-align: center;">We received a Security PIN reset request.</td>
</tr>
<tr>
	<td width="40">&nbsp;</td>
</tr>

<tr>
	<td align="center"><p><b>Your OTP: </br><strong style="font-size:25px; display:inline-block;text-align: center;color: #009900">{{$code}}</strong></p></td>
</tr>
<tr>
    <td  align="center" style="font-size:14px;color:#929292">
    <p>Virob support teams will never ask you to send or confirm your Security PIN, Passwords or personal information via email. If you believe an unauthorized person accessed your account, please contact <strong  style="font-size:14px;color:#929292;">support@virob.com.</strong></p>
    </td>
</tr>
@stop