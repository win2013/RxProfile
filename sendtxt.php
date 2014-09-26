<?php


echo "Sending text messages ";
$con = mysql_connect("localhost","root","aZdcgB");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
$txtURL = "http://www.ubiwireless.com/mobile/email.php?Subject=REMINDER&";

mysql_select_db("ufpdt", $con);

$getREminderUrl="http://rxprofile.net/index.php?Screen=getReminder";
$result = mysql_query("SELECT * from Reminders, Medication where Reminders.MedicationID = Medication.MedicationID and Reminders.ScheduleToSend between DATE_ADD(NOW(), INTERVAL -14 MINUTE) and DATE_ADD(NOW(), INTERVAL 14  MINUTE) ");

if ($result)
while ($row = mysql_fetch_assoc($result)) {

  $now    = strtotime("now");
  $tsend  = strtotime($row['ScheduleToSend']);

  print_r($row);

  if (strpos($row['SentTime'], "0000-00-00")>=0)
  {
        $a2fme = "http://a2f.me/silenturl.php?url=".urlencode("http://rxprofile.net/index.php?screen=getReminder&ReminderID=".$row['ReminderID']."&DrugName=".$row['Drug']);
	echo " ITEM TO A2MR............".$a2fme."\n";	
        $url = file_get_contents($a2fme);
        echo " A2F.me : ".$url."\n";
        $query_pat   = "SELECT * from Patient_Demographics where MedicaidID = ".$row['PatientID'];
        echo "Query : ".$query_pat."\n";
        $result2 = mysql_query($query_pat);
        if ($row2 = mysql_fetch_assoc($result2))
        {
              echo "Preping to text : ";
              if (strpos($row2['CARRIER'],"att")>=0)
              {
                $txtURL = $txtURL."to=".$row2[PHONENUMBER].'@txt.att.net&body=';
                $txtURL = $txtURL.urlencode("RxProfile reminds you: ".$url);
                $response = file_get_contents($txtURL);
                echo "Sending text .... ".$response. " with ".urlencode("RxProfile reminds you:".$url);
                $qry_reminder = "UPDATE Reminders Set SentTime=NOW(), TxtMessage='RxProfile Reminds you : ".$url."' WHERE  ReminderID=".$row['ReminderID'];
                $r2 = mysql_query($qry_reminder);
                if ($r2) 
                     echo "Accepted ".mysql_num_rows($r2)." Query : ".$qry_reminder; 
              }
        }
   }

}

mysql_close($con);
?>
