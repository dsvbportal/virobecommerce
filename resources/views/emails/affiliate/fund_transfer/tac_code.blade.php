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
<tr>
    <td align="center" style="font-size:30px;color:#000">Fund Transfer OTP</td>
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
		<td style="font-size:14px;" align="center">
		<p>Use the OTP <b><?php echo $tac_code;?></b> to transfer the amount to username: (Uname). OTP is valid for 5 minutes.</p>                            
	 </td>
   </tr>
  <tr><td  align="center"  style="font-size:14px;color:#929292"><p>If the changes described above are accurate, no further action is needed.</p>
  <p>If you do not recognize this request, feel free to ignore this email or contact support.</p></td></tr>
@stop 
            