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
    const UPDATE     = "UPDATE ";
    const WHERE      = " WHERE ";
    const MEDTABLE   = "Medication";
    const MEDID      = "MedicationID";
    const MEDSIDE    = "Medication_Side_Effects";
    const SIDEID     = "Medicine"; // Use the Name to find the Drug not the ID
    const INTERTABLE = "Interactions";
    const INTERID    = "InteractionID";
    const INTERKEY   = "Drug";
    const PATIENTKEY = "Patient";
    
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
        if ($_SESSION[self::PATIENTKEY]==true)
            $this->bPatient = true;
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
        echo $sql;
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
               $this->iui->Button_href("redButton", "index.php?screen=deleteAdverse&id=".$row->HerbID, "Delete Reaction");
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


    public function drawReactionReport($id, $medid){
        // $this->isAdverseReaction()
    }

    public function setMyInfo($id, $update)
    {
        // $update is an associative arry with the data to update
        $where[self::MEDID] = MySQL::SQLValue($id, "integer");
        $result = $mydb->UpdateRows(self::MEDTABLE, $update, $where);
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

          $this->iui->ul_start("home", "Rx Profile:".$Medication->Drug, true);
          $this->iui->Header("2","Medication :".$Medication->Description);
          $this->iui->li_start_ref("#Modify", "Modify description");
          $this->iui->li_start_ref("#SideEffects", " See Side Effects");
          $this->iui->li_start_ref("#AdverseReaction", "Adverse Reactions");
          $this->iui->li_start_ref("#CheckReaction", "Reaction with ID: ".$this->login);

          //$this->iui->li_start_ref("#")

          if ($this->bPatient != true ) {
              $this->iui->li_start_ref("#AddSideEffect", "Add Side Effects");
              $this->iui->li_start_ref("#RemovefromDB", "Delete this item");
          }
          $this->iui->ul_stop();

          $this->iui->Form_start("Modify", "Modify", "index.php?screen=updateMedication&id=".$this->id, false);
          $this->iui->Fieldset_start();
          $this->iui->Header("2", "Drug Id :".$this->id);
          $this->iui->inputtext_label("DrugName :", "text", "Drug", $Medication->Drug);
          $this->iui->inputtext_label("Description :", "text", "Description", $Medication->Description);
          $this->iui->Fieldset_stop();
          // If a patient is looking at record then, allow to add it to his profile
          // if not it's a pharmacist who can modify or delete record.                    
          $this->iui->Form_Stop();
          
          $this->drawSideEffects($Medication->Drug);
          $this->drawAdverseReactions($Medication->Drug);          
          $this->drawReactionReport($this->id, $this->login);
    }
}


?>
