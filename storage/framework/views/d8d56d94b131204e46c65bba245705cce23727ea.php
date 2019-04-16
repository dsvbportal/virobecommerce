<!DOCTYPE html>
<html lang="en">
<head>
<!-- ============ META =============== -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Virob</title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="robots" content="">
<meta name="author" content="">
<!-- ============ FAVICON =============== -->
<link rel="icon" href="<?php echo e(asset("assets/images/favicon/favicon.png")); ?>">
<style type="text/css">

body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 20px;
	color: #00CCFF;
	background:url(<?php echo e(asset("resources/assets/static/images/bg/10.jpg")); ?>) transparent fixed no-repeat;
}
.overlay{
    position:fixed;
    top:0;
    bottom:0;
    left:0;
    right:0;
    z-index:1;
    background-color:rgba(0,0,0,.8);
}
</style>
</head>
<body style="">
<div class="overlay">
<table border="0" align="center" style="width:500px; height:200px;margin-top:-100px;top:50%; position:absolute;left:50%; margin-left:-250px;">
  <tr>
    <td align="center">
	<h3>Oops! Page not found.</h3>
		<p style="font-size:14px; color:#ffffff;font-weight:normal">
			We could not find the page you were looking for.            </p>
	</td>
  </tr> 
</table>
</div>
</div>
</body>
</html>
