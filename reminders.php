<?php

echo " Query... for medication for today ";
$con = mysql_connect("localhost","root","aZdcgB");
if (!$con)
{
  die('Could not connect: ' . mysql_error());
}

mysql_select_db("ufpdt", $con);

$result = mysql_query("SELECT * from Patient_Medications");
$mainqr = "INSERT INTO Reminders (PatientID, MedicationID, ScheduleToSend, SentTime) ";
while ($row = mysql_fetch_assoc($result))
{
   $date = sprintf("%02d:%02d:%02d", date('Y'), date('m'), date('d'));
   //echo $date. "strtotime: ".strtotime($date)." Now=".strtotime("now");
   print_r($row);
   $now    = strtotime("now");
   $tends  = strtotime($row['DateTreatmentEnds']);
   if ($tends >= $now)
   {
       $i=0;
       $directions = $row['Directions'];
       $pos = strpos($directions, "Morning",0);
       echo "Directions = ".$directions." Pos = ".$pos;
       if ($pos !== false)  {
           $scctime = $date." 6:00:00";
           $qr[$i] =  $mainqr." VALUES ("."'".$row['MedicaidID']."'".","."'".$row['DrugID']."'".","."'".$scctime."'".",  '0000-00-00 00:00:00')";
           $i++;
       } 
       
       if (strpos($directions, "Evening" )!==false) {
           $scctime = $date." 18:00:00";
           $qr[$i]= $mainqr." VALUES ("."'".$row['MedicaidID']."'".","."'".$row['DrugID']."'".","."'".$scctime."'".", '0000-00-00 00:00:00')";
           $i++;
       }
       if (strpos($directions, "Noon")!==false) {
           $scctime=$date." 12:00:00";
           $qr[$i]= $mainqr." VALUES ("."'".$row['MedicaidID']."'".","."'".$row['DrugID']."'".","."'".$scctime."'".", '0000-00-00 00:00:00')";
           $i++;
       }
       if (strpos($directions, "Bedtime")!==false) {
           $scctime=$date." 22:00:00";
           $qr[$i] = $mainqr." VALUES ("."'".$row['MedicaidID']."'".","."'".$row['DrugID']."'".","."'".$scctime."'".",  '0000-00-00 00:00:00' )";
           $i++;
       }
       if (strpos($directions, "24 hrs")!==false) {
           $scctime=$date." 12:00:01";
           $qr[$i] =  $mainqr." VALUES ("."'".$row['MedicaidID']."'".","."'".$row['DrugID']."
'".","."'".$scctime."'".",  '0000-00-00 00:00:00' )";
           $i++;

       }

       for ($j=1; $j<=$i; $j++) {
           echo "Execute ... Query #".$j." \n".$qr[$j-1]." \n";
	   $res =  mysql_query($qr[$j-1]); 
           if ($res) {
              echo "Rows affected : ".mysql_num_rows($res)."\n";
           } else {
              echo "Error : ".mysql_error()."\n";
           }
        }
   }
}

echo "Query completed!";

?>


