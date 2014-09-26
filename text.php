<?php

$txtURL = "http://www.ubiwireless.com/mobile/email.php?to=5613064996@txt.att.net&Subject=REMINDER&body=";
$result = file_get_contents("http://a2f.me/silenturl.php?url=".urlencode("http://rxprofile.net/init.php?Screen=getReminder&ReimiderID=0")); 
$txtURL = $txtURL.urlencode("RxProfile reminds you: ".$result); 
$result = file_get_contents($txtURL);

?>

