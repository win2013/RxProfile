<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Description of Medications
 *
 * @author Edwin A. Hernandez
 * @@property UBIWIRELESS, LLC (c) 2009
 *
 */

class Medications {
    //put your code here
    private $mydb;
    private $iui;
    private $login;
    private $bPatient = false;

    const SEFROM     = "SELECT * FROM ";
    const MEDTABLE   = "Medication";
    const MEDID      = "Drug";
    const WHERE      = " Where ";
    const NAME_LIST  = "Medication";

    const PATIENTKEY = "Patient";

    public function initialize($login, $db, $iui )
    {
        $this->iui  = $iui;
        $this->mydb = $db;
        if ($_SESSION[self::PATIENTKEY]!="None")
            $this->bPatient = true;
        $this->login = $login;
    }

    public function drawThis(){
          $j = 0;
          $this->iui->ul_start(self::NAME_LIST, self::NAME_LIST, true);
          if ( $this->bPatient == false)
          {
              $this->iui->li_start_ref("index.php?screen=AddMedication", "New Medication");
          }          
          for ($i = 65; $i<=90; $i++)
          {
              $alphabet[$j] = chr($i);
              //echo $aphablet[$j]."   ".chr($i)."  index=".$i;
              $sql[$j] = self::SEFROM.self::MEDTABLE.self::WHERE.self::MEDID." like \"".$alphabet[$j]."%\"";
              //$this->iui->li_start(true, $alphabet[$i]);
              // Loop through the records using the MySQL object (prefered)
              $this->iui->li_start_ref("#".$alphabet[$j], " Starting with ".$alphabet[$j]);
              $j=$j+1;
          }
          $this->iui->ul_stop();

          for ($x = 0; $x<$j; $x++) {
            if (!$this->mydb->Query($sql[$x]))
                $this->mydb->Kill();
            $this->iui->ul_start($alphabet[$x], $alphabet[$x], false);
            $this->mydb->MoveFirst();
            while (! $this->mydb->EndOfSeek()) {
                    $row = $this->mydb->Row();
                    $this->mydb->SeekPosition();
                    $this->iui->li_start_ref("index.php?screen=getMedication&id=".$row->MedicationID, $row->Description);
            }
            $this->iui->ul_stop();
          }
    }

}
?>
