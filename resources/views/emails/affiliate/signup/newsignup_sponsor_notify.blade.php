@extends('emails.affiliate.maillayout')
@section('content')
   <tr>
	<td align="center" style="font-size:15px;font-weight:bold;color:#008000">Dear <?php echo $referral_fullname;?>,</td>
	</tr>
	 <tr>
		<td width="40">&nbsp;</td>
	</tr>
	<tr>
	<td align="center"  style="font-size:15px;font-weight:bold;color:#008000">User Name - <?php echo $referral_name;?>,</td>
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
		<td align="center" >
		 <span class="subtitle"> An affiliate has accepted your invitation and Registered with <?php echo $pagesettings->site_name;?></span>
	   </td>
	</tr>
	 <tr><td align="center" >
		<p><b>Name :</b><?php echo $fullname;?></p>		
		<p><b>Country   :</b><?php  echo $country;?></p>
		<p><b>State/Region     :</b><?php  echo $state;?></p>
		
		</td>
	</tr>
@stop