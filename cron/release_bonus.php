<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://dreamsgaincoin.com/CScheduler/index");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);
curl_close ($ch);
?>