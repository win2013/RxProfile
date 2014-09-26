<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 */
session_start();
/**
 * Description of Medication
 *
 * @author Edwin A Hernandez
 * @copyright UBIWIRELESS (c)2009
 * 
 */


class Medication
{
    //put your code here
    private $values;

    const SEFROM     = "SELECT * FROM ";
    const INSERT     = "INSERT INTO ";
    const UPDATE     = " UPDATE ";
    const WHERE      = " WHERE ";
    const MEDTABLE   = "Medication";
    const MEDID      = "MedicationID";
    const MEDIRTABLE = "Medication_Directions";
    const MEDSIDE    = "Medication_Side_Effects";
    const SIDEID     = "Medicine"; // Use the Name to find the Drug not the ID
    const INTERTABLE = "Interactions";
    const INTERID    = "InteractionID";
    const INTERKEY   = "Drug";
    const PATIENTKEY = "Patient";
    const PATMEDTABLE= "Patient_Medications"; 
    const HERTABLE   = "Patient_Herbs";
    const HERID      = "HerbID";
    const DRUGS      = "Medication";
    
    // MySQL content from mysql.class file
    private $mydb;
    private $id;
    private $login;
    private $iui;

    public function initialize($login, $id, $db, $iui)
    {
        $this->iui = $iui;
        $this->mydb = $db;
        $this->id = $id;
        $this->login = $login;
        if ($_SESSION[self::PATIENTKEY]=='None')
            $this->bPatient = false;
        else 
        	  $this->bPatient = true;
    }

    public function isMedicationThere($id, $drugID){ 
        $query = self::SEFROM.self::PATMEDTABLE.", ".self::DRUGS.self::WHERE." Patient_Medications.MedicaidID = '".$id."' AND Medication.MedicationID = Patient_Medications.DrugID  AND Patient_Medications.DrugID = '".$drugID."'";
        //echo $query;
        $this->mydb->Query($query);
        if ($this->mydb->RowCount()==0)
        	  return false;
        else 
        	   return true; 
    }	

    public  function getMedication($id) {
         $sql = self::SEFROM.self::MEDTABLE.self::WHERE.self::MEDID." = '".$id."'";
         //echo $sql;
         if (! $this->mydb->Query($sql) )    $this->mydb->Kill();
    }

    public function getSideEffects($medName) {
        $sql = self::SEFROM.self::MEDSIDE.self::WHERE.self::SIDEID." = '".$medName."'";
        //echo $sql;
        if (! $this->mydb->Query($sql) )    $this->mydb->Kill();
    }

    public function getAdverseReaction($medName) {
        $sql = self::SEFROM.self::INTERTABLE.self::WHERE.self::INTERKEY."='".$medName."'";
        //echo $sql;
        if (! $this->mydb->Query($sql) )    $this->mydb->Kill();
    }

    public function drawAdverseReactions($medName) {
       $this->getAdverseReaction($medName);
       $this->mydb->MoveFirst();
       $this->iui->ul_start("AdverseReaction", "Reactions", false);

       while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->li_start_ref("#Med".$row->InteractsWith, $row->InteractsWith);           
        }
       $this->iui->ul_stop();

       $this->mydb->MoveFirst();
        while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->div_start("Med".$row->InteractsWith, "panel", $row->Drug);
               $direct = ' Medication : '.$row->Drug. 'and Generic name: '.$row->GenericName;
               $direct = $direct.' interacts with '.$row->InteractsWith.' Why? '.$row->Why;
               $direct = $direct. " Comment : ".$row->Comment;
               $this->iui->Header("2", $direct);
               //if ($this->bPatient == false) {
                   $this->iui->Button_href("redButton",  "index.php?screen=deleteAdverse&id=".$row->HerbID, "Delete Reaction");
               //    $this->iui->Button_href("whiteButton","index.php?
              // } 
               $this->iui->div_close();
        }        
    }


    public function drawSideEffects($medName) {
        $this->getSideEffects($medName);
        $this->mydb->MoveFirst();
        $this->iui->div_start("SideEffects", "panel", $medName);
        while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->Header("2", $row->SideEffect, " Percentage:", $row->Percentage);
        }
        $this->iui->div_close();
    }


   
    public function drawReactionReport($id, $medid){  // For a patient with drug $id, and $medid asa medicine ID 
        // $this->isAdverseReaction()
        
    }

   	public function getDirections(){ 
	  	 $sql = self::SEFROM.self::MEDIRTABLE;
	  	 //echo $sql;
   	   $res = $this->mydb->Query($sql);  
   	   while (!$this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $options[$row->Directions] = $row->Directions;
        } 
        return $options;
	  }
	  
	  public function drawAddNewMedicationPatient($id, $medName) {
	  	    $options = $this->getDirections();
	  		  $this->iui->Form_start("addNewMedicationPatient", "addNewMedicationPatient", "index.php?screen=addMedicationToPatient&id=".$_SESSION['patient'], false);
          $this->iui->Fieldset_start();
          $this->iui->Header("2", "Drug Name :".$medName);        
          $this->iui->Select("Directions", $options);
          $this->iui->inputtext("hidden", "MedicaidID", $_SESSION['patient']);
          $this->iui->inputtext("hidden", "DrugID",                      $id);  
          $this->iui->inputtext_label("More :", "text", "SpecialDirections", "None");
          $this->iui->Button_submit("Update Record");
          $this->iui->Fieldset_stop();
          $this->iui->Form_Stop();
	  }

    public function setMyInfo($id, $update)
    {
        // $update is an associative arry with the data to update
        $where[self::MEDID] = MySQL::SQLValue($id, "integer");
        $result = $this->mydb->AutoInsertUpdate(self::MEDTABLE, $update, $where);
        if (! $result) {
             // Show the error and kill the script
           $this->mydb->Kill();
        }
    }

    public function drawThis($bPatient)
    {
          $j = 0;
          $this->getMedication($this->id);
          $Medication = $this->mydb->Row();
          $login = $_SESSION['patient'];      
          $this->iui->ul_start("home", "Rx:".$Medication->Drug, true);
          $this->iui->Header("2","Medication :".$Medication->Description);
          $this->iui->li_start_ref("#Modify",          "View/Modify description");
          $this->iui->li_start_ref("#SideEffects",     "See Side Effects");
          $this->iui->li_start_ref("#AdverseReaction", "See Adverse Reactions");
          $this->iui->li_start_ref("#CheckReaction",   "Potential Reactions with me");
          //$this->iui->li_start_ref("#")

          if ($this->bPatient != true ) {
              $this->iui->li_start_ref("#AddSideEffect", "Add Side Effects");
              $this->iui->li_start_ref("#RemovefromDB",  "Delete this medication ");
              $this->iui->li_start_ref("#CreateMedicine","Create New Entry "); 
          }
                 
          if ($this->bPatient == true) {      	
          	  if ($this->isMedicationThere($login, $this->id)==true) {
          	      $this->iui->Button_href("redButton", 
                                             "index.php?screen=deletePatientMedication&patientid=".$login."&id=".$this->id,           	      												"Delete Medication");
          	  } else 
          	      $this->iui->Button_href("whiteButton", "#addNewMedicationPatient", "Add Medication");  
          }
          
          $this->iui->ul_stop();
          $this->iui->Form_start("Modify", "Modify", "index.php?screen=updateMedication", false);
          $this->iui->Fieldset_start();
          $this->iui->Header("2", "Drug Id :".$this->id);
          $this->iui->inputtext_label("DrugName :", "text", "Drug", $Medication->Drug);
          $this->iui->inputtext_label("Description :", "text", "Description", $Medication->Description);
          $this->iui->inputtext("hidden", "MedicationID", $this->id);
          $this->iui->Button_submit("Update Medication"); 
          $this->iui->Fieldset_stop();
          // If a patient is looking at record then, allow to add it to his profile
          // if not it's a pharmacist who can modify or delete record.                    
          $this->iui->Form_Stop();
          $this->iui->
          
          $this->drawSideEffects($Medication->Drug);
          $this->drawAdverseReactions($Medication->Drug);          
          $this->drawReactionReport($this->id, $this->login);
          $this->drawAddNewMedicationPatient($this->id, $Medication->Drug);
          //$this->drawAddNewMedication();
          
    }
}


?>
