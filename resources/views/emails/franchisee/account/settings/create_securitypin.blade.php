@extends('emails.franchisee.maillayout')
@section('content')
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$full_name}},</td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">User Name - {{$uname}}, </td>
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
    <td align="center" style="font-size:30px;color:#000">Your Security PIN has been created!</td>
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
    <td align="center" style="font-size:14px;color:#929292">This is to inform that your Security Pin  was created by you or by someone logged in using your login and password on <strong>{{$last_activity}}</strong> from IP-<strong></strong>. </p>
    <p>If you didn't change your profile data please contact the support team of immediately.</p></td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
@stop
