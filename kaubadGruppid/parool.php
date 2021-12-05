<?php
$parool='admin';
$sool='12346';
$krypt=crypt($parool, $sool);
echo $krypt;