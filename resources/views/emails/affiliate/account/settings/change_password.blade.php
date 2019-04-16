@extends('emails.affiliate.maillayout')
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
    <td align="center" style="font-size:30px;color:#000">Password Reset Notification</td>
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
    <td align="center" style="font-size:14px;color:#929292">Your new Virob password has been set. <br>Login Details <strong>{{$last_activity}}</strong> from IP - <strong>{{$client_ip}}</strong>. </p>
    <p>If the changes described above are accurate, no further action is needed.</p>
	<p>If you do not recognize this request, feel free to ignore this email or contact support.</p></td>
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
@stop
