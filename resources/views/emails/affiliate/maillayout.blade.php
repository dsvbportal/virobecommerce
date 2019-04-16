<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>@yield('title')</title>
        <style type="text/css">
            .ExternalClass {font-size:1px;}
            table {border-collapse:inherit}
            .ReadMsgBody {width: 100%;}
            .ExternalClass {width: 100%;}
            h2,h2 a,h2 a:visited,h3,h3 a,h3 a:visited,h4,h5,h6,.t_cht {color:#006E12 !important}
            div, p, a, li, td { -webkit-text-size-adjust:none; }
            table td { border-collapse: collapse; }
        </style>
    </head>
    <body bgcolor="#E6EBEE" style="table-layout:fixed;">
        <div style="width:100%;background-color:#E6EBEE;">
            <table width="100%" bgcolor="#E6EBEE" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr><td bgcolor="#E6EBEE">
                        <table width="763" cellpadding="0" cellspacing="0" border="0" align="center">                           
                      
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
										<tr>
                                            <td width="61"></td>
                                            <td width="641" height="60" valign="middle" bgcolor="#cad5db">
											<div style="text-align:center; font-family:Tahoma, Geneva, sans-serif;"><span style=" text-align:center;color:#000;">{{date('M d, Y H:i:sT')}}</span></div>
                                            </td>
                                            <td width="61"></td>
                                        </tr>
                                        <tr>
                                            <td width="61" valign="bottom"></td>
                                            <td width="641"style="font-size:14px;color:#000000" align="center">
											
										
											<table width="641" cellpadding="0" cellspacing="0" border="0" style="font-family:Tahoma, Geneva, sans-serif;">
                                                    <tr>
                                                        <td width="41" bgcolor="#FFFFFF"></td>
                                                        <td width="559" bgcolor="#CAD5DB">
                                                            <table width="559" cellpadding="0" cellspacing="0" border="0">
                                                                <!-- EVENTs Start -->																
                                                                <tr>
                                                                    <td width="10"></td>
                                                                    <!-- EVENTs 1 Start -->
                                                                    <td width="250" valign="top">
                                                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                                            <tr>
                                                                                <td width="2%" bgcolor="#FFFFFF"><img src="{{URL::asset('resources/assets/img-email/box-corner-top-left.png')}}" alt="" width="20" height="20" style="" /></td>
                                                                                <td width="210" bgcolor="#FFFFFF"></td>
                                                                                <td width="2%" bgcolor="#FFFFFF" align="right"><img src="{{URL::asset('resources/assets/img-email/box-corner-top-right.png')}}" alt="" width="20" height="20" style="" /></td>
                                                                            </tr>
																			
                                                                            <tr>
                                                                                <td width="20" bgcolor="#FFFFFF"></td>
                                                                                <td width="210" bgcolor="#FFFFFF">
																					<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr><td align="center" valign="top" height="85">
											<img src="{{asset('resources/assets/img-email/logo.png')}}" />	</td>
											</tr>
											</table>
                                                                                	<table style="padding:20px;width:670px"  cellspacing="0" cellpadding="0" border="0" bgcolor="#fff" align="center">
																					<tbody>
                                                                                    @yield('content')
																					<tr><td width="40">&nbsp;</td></tr>
																					<tr><td bgcolor="#d7d7d7" height="1"></td></tr>
																					<tr><td width="40">&nbsp;</td></tr>
																					<tr><td align="left">
																					<p>Cheers<br>
																					<b>Team VIROB</b><br>
																					<a href="https://virob.com">www.virob.com</a></p>
																					</td></tr>
                                                                                    </tbody>
                                                                                    </table>
                                                                                </td>
                                                                                <td width="20" bgcolor="#FFFFFF"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="20" bgcolor="#FFFFFF"><img src="{{URL::asset('resources/assets/img-email/box-corner-bottom-left.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                                <td width="210" bgcolor="#FFFFFF"></td>
                                                                                <td width="20" bgcolor="#FFFFFF" align="right"><img src="{{URL::asset('resources/assets/img-email/box-corner-bottom-right.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td width="20"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="3%"><img src="{{URL::asset('resources/assets/img-email/box-blue-corner-bottom-left.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                    <td width="250">&nbsp;</td>
                                                                    <td width="3%" align="right"><img src="{{URL::asset('resources/assets/img-email/box-blue-corner-bottom-right.png')}}" alt="" width="20" height="20" style="display:block;" /></td>
                                                                </tr>
                                                                <!-- EVENTs End -->
                                                            </table>
                                                        </td>
                                                        <td width="41" bgcolor="#FFFFFF"></td>
                                                    </tr>
                                                </table></td>
                                            <td width="61" valign="bottom"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>  
							<tr><td height="20"></td></tr>
                            <!-- FOOTER Start -->
                            <tr>
                                <td width="763" valign="top">
                                    <table width="763" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="61"></td>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td align="center" style="text-align:center;line-height:18px;"><span style="font-family:Tahoma, Geneva, sans-serif;font-size:12px;color:#333F44;text-decoration:none;">This is an auto-generated email. Please do not reply <br>Please contact <a href="mailto:support@virob.com">support@virob.com</a> for any queries regarding this.<br>If you think this is SPAM, please report to <a href="mailto:support@virob.com">abuse@virob.com</a> for immediate action.</span></td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td height="15" align="center" style="font-size:10px;color:#373737; font-family:Tahoma, Geneva, sans-serif;">
                                                            <p>{{ $pagesettings->footer_content }}</p>
                                                        </td>
                                                    </tr> -->
                                                </table>
                                            </td>
                                            <td width="61"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>					
                </tr>
            </table>
        </div>
    </body>
</html>