@extends('emails.franchisee.maillayout')
@section('content')
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$full_name}},</td>
</tr>
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Username  - {{$uname}}, </td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
<tr>
    <td align="center" style="font-size:15px;color:#000;text-align: center;">Your new Virob password has been set.</td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
<tr>
    <td align="center" style="font-size:14px;color:#929292">Login Details: {{$last_activity}} from IP - {{$client_ip}}</strong>
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
