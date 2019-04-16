@extends('emails.franchisee.maillayout')
@section('content')
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Dear {{$userSess->full_name}},</td>
</tr>
<tr>
    <td align="center" style="font-size:15px;font-weight:bold;color:#000">Username  - {{$userSess->uname}} </td>
</tr>
<tr>
    <td width="40">&nbsp;</td>
</tr>
<tr> <td style="font-size:14px;font-weight:bold;color:#000;text-align: center;">
        <p>Your Mobile number has been successfully updated.</p>
    </td>
</tr>
<tr> <td style="font-size:14px;color:#929292;text-align: center;">
        <p>If the changes described above are accurate, no further action is needed.</p>        
		<p>If you do not recognize this request, feel free to ignore this email or contact support.</p>
    </td>
</tr>
@stop