<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
/**
 * Description of Patient
 *
 * @author Edwin A Hernandez
 * @copyright UBIWIRELESS (c)2009
 */
class Patient {
    //put your code here    
    const SEFROM     = "SELECT * FROM ";
    const INSERT     = "INSERT INTO ";
    const UPDATE     = "UPDATE ";
    const WHERE      = " WHERE ";
    const PATTABLE   = "Patient_Demographics";
    const PATID      = "MedicaidID";
    const MEDTABLE   = "Patient_Medications";
    const MEDID      = "MedicaidID";
    const HERTABLE   = "Patient_Herbs";
    const DRUGID     = "DrugID";
    const HERID      = "HerbID";
    const PATCONDS   = "Patient_Conditions";
    const DRUGS      = "Medication";
    const REMITABLE  = "Reminders";

    private $id;    
    // MySQL content from mysql.class file
    private $mydb;
    private $iui;

    public function initialize($id = null, $db = null, $iui)
    {      
       if ($db == null) {
           $this->mydb = new MySQL();
           if ($this->mydb->Error()) $$this->mydb->Kill();
       }
       else
           $this->mydb = $db;

       if ($id != null)
            $this->id = $id;
       else
            $this->id = 0;

       $this->iui = $iui;
     }

    private function doQuery($query){
        if (! $this->mydb->Query($query) )
            $this->mydb->Kill();
        //return $this->mydb->GetXML();
    }

    public function getMyInfo() {
        $query = self::SEFROM.self::PATTABLE.self::WHERE.self::PATID." = '".$this->id."'";
        return $this->doQuery($query);
    }

    public function setMyInfo($id, $update)
    {
        // $update is an associative arry with the data to update
        $where[self::PATID] = MySQL::SQLValue($id, "integer");
        //$this->iui->ul_start("setMyInfo", "SetMyInfo", true);        
        //$this->iui->li_start_ref("#SetMyInfo", "SetMyInfo");
        //$this->iui->li_start_ref("#Log2", "Medicaid ID".$id);
        //$this->iui->li_start_ref("#Log1", print_r($update));
        $result = $this->mydb->AutoInsertUpdate(self::PATTABLE, $update, $where);
        //$this->iui->li_start_ref("#Log3", self::PATTABLE." where =".print_r($where). " result = ".$result);
        //$this->iui->ul_stop();
        if (! $result) {
             // Show the error and kill the script
           $this->mydb->Kill();
        }
    }


    public function deletePatientMedication($medicaidid, $drugid)
    {
        $where = array ( self::MEDID   => $medicaidid, 
                         self::DRUGID  => $drugid );
        //print_r($where);                  
        $this->mydb->DeleteRows(self::MEDTABLE, $where);
    }

    public function getAllMedications($id)
    {
        $query = self::SEFROM.self::MEDTABLE.", ".self::DRUGS.self::WHERE." Patient_Medications.MedicaidID = '".$id."' AND Medication.MedicationID = Patient_Medications.DrugID ";
        //echo $query;
        return $this->doQuery($query);
    }
   
    public function insertintoMedication($values) 
    {
    	$sql = self::INSERT.self::MEDTABLE." (MedicaidID, DrugID, Directions, SpecialDirections) ";
    	$sql = $sql." VALUES ( '". $values['MedicaidID']."','".$values['DrugID']."','".$values['Directions']."'";
    	$sql = $sql.",'". $values['SpecialDirections']."') ";
    	 // echo $sql; 
        $this->doQuery($sql);
       $this->iui->ul_start("setMyInfo", "SetMyInfo", true);        
       $this->iui->li_start_ref("#Log1", print_r($values));
       $this->iui->li_start_ref("#Log3", $sql);
       $this->iui->ul_stop();	 
    }  
   

    public function isMedicationThere($id, $drugID){ 
        $query = self::SEFROM.self::MEDTABLE.", ".self::DRUGS.self::WHERE." Patient_Medications.MedicaidID = '".$id."' AND Medication.MedicationID = Patient_Medications.DrugID  AND Patient_Medication.DrugID = '".$drugDI."'";
        echo $query;
        $this->doQuery($query);
        if ($this->mydb->RowCount() == 0)
        	return false;
        else 
        	return true; 
    }	
    
    
    public function getAllHerbs($id)
    {
        $query = self::SEFROM.self::HERTABLE.",Herbs ".self::WHERE." Patient_Herbs.MedicaidID = ".$id." AND Herbs.HerbID = Patient_Herbs.PatHerbID";
        echo $query;
        return $this->doQuery($query);
     }

    public function getConditions($id){
        $query = self::SEFROM.self::PATCONDS.self::WHERE.self::MEDID." = ".$id;
			  //echo $query;
        return $this->doQuery($query);
    }
    
    public function getAppointments(){
        $query = SEFROM.PATTABLE.WHERE.PATCOL." = ".$id;
        if (!$this->mydb->query($query))
           $this->mydb->dbKill();
      // return $mydb->GetXML();
    }

   public function getAllReminders($id)
   {
       $query = self::SEFROM.self::REMITABLE.",".self::DRUGS." ".WHERE." Reminders.PatientID = ".$id. " AND Medication.MedicationID = Reminders.MedicationID";
       echo $query;
       return $this->doQuery($query);
   }

   public function drawAllReminders() {
        $this->mydb->MoveFirst();
        $this->iui->ul_start("Reminders", "Reminders", false);
        while (!$this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->li_start_ref("#Remindersid".$row->ID, $row->Drug);
        }
        $this->iui->ul_stop();

        $this->mydb->MoveFirst();
        while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->div_start("Remindersid".$row->ID, "panel", $row->Drug);
               $direct = 'You have to take '.$row->Drug.' Time: '.$row->ScheduleToSend.' as prescribed by your doctor on'.$row->Directions.'. Medication details :'.$row->Description;
               $this->iui->Header("2", $direct);
               $this->iui->Button_href("whiteButton", "index.php?screen=getMedication&id=".$row->DrugID, "More information");
               $this->iui->Button_href("redButton", "index.php?screen=deletePatientMedication&patientid=".$this->id."&id=".$row->DrugID, "Delete from Patient ". $this->id );
               $this->iui->Button_href("whiteButton", "index.php?screen=getReminder=".$row->ReminderID&"&DrugNane=".$row->Drug, "Reminder");
               $this->iui->div_close();
        }

   }

    public function drawAllMedications(){
        $this->mydb->MoveFirst();
        $this->iui->ul_start("Medications", "Medications", false);
        while (!$this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->li_start_ref("#Medication".$row->DrugID, $row->Description);
        }
        $this->iui->ul_stop();

        $this->mydb->MoveFirst();
        while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->div_start("Medication".$row->DrugID, "panel", $row->Drug);
               $direct = 'You have to take '.$row->Drug.' on '.$row->Directions.'. Medication details :'.$row->Description;
               $direct = $direct ." Your adherence to this medication thus far is ".$row->Adherence."%";
               $direct = $direct. " Potential side effects of this drug are :".$row->SideEffects;
               $this->iui->Header("2", $direct);
               $this->iui->Button_href("whiteButton", "index.php?screen=getMedication&id=".$row->DrugID, "More information");
               $this->iui->Button_href("redButton", "index.php?screen=deletePatientMedication&patientid=".$this->id."&id=".$row->DrugID, "Delete from Patient ". $this->id );
               $this->iui->div_close();
        }

    }


     public function drawAllHerbs(){
        $this->mydb->MoveFirst();
        $this->iui->ul_start("Herbal", "Herbs", false);
        while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->li_start_ref("#Herb".$row->HerbID, $row->Herb);
               $this->iui->div_close();
        }
        $this->iui->ul_stop();
        $this->mydb->MoveFirst();

         while (! $this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->div_start("Herb".$row->HerbID, "panel", $row->Herb);
               $direct = 'You are taking also '.$row->Herb;
               $this->iui->Header("2", $direct);
               $this->iui->Button_href("whiteButton", "index.php?screen=getHerb&id=".$row->HerbID, "More information");
               $this->iui->Button_href("redButton", "index.php?screen=deleteHerb&id=".$row->HerbID, "Delete from Patient ".  $this->id );
               $this->iui->div_close();
        }
    }

    public function drawAllConditions() {
        // Conditions:: 
        $this->mydb->MoveFirst();
        $this->iui->ul_start("Conditions", "Conditions", false);
        while (!$this->mydb->EndOfSeek()) {
               $row = $this->mydb->Row();
               $this->mydb->SeekPosition();
               $this->iui->li_start_ref("#Condition".$row->ID, $row->Conditions);
        }
        $this->iui->ul_stop();
    }

    public function drawPatient()
    {
        $this->getMyInfo();
        $patient = $this->mydb->Row();
        //print_r($patient);
        $id = $patient->MedicaidID;
        $this->iui->ul_start("home", "Rx Profile", true);
        $this->iui->li_start_ref("#Patient", "Patient Profile");
        $this->iui->li_start_ref("#Medications", "My Medications");
        $this->iui->li_start_ref("#Herbal", "Herbal Supplements");
        $this->iui->li_start_ref("#Conditions", "Conditions");
        $this->iui->li_start_ref("#Reminders" , "Reminders ");
        //$this->iui->li_start_ref("index.php?screen=AddMedication&id=".$id, "Add New Medication");       
        $this->iui->Button_href("whiteButton", "index.php?screen=showMedications&id=".$id, "Add New Medication");
        $this->iui->ul_stop();
        $this->iui->Form_start("Patient", "panel", "index.php?screen=UpdatePatient&id=".$id, false);
        //$this->iui->Form_start("Patient", "panel", "getPost.php?id=".$id, false);

        $this->iui->Header("2", "Rx: Patient Profile");
        $this->iui->Fieldset_start();        
        $this->iui->label("Medicaid ID: ".$patient->MedicaidID);
        $this->iui->inputtext_label("First Name: ",  "text", "FirstName",  $patient->FirstName);
        $this->iui->inputtext_label("Last Name: ",   "text", "LastName",   $patient->LastName);
        $this->iui->inputtext_label("Address :", "text", "Address",        $patient->Address);
        $this->iui->inputtext_label("Apt/Other :","text", "Address2",      $patient->Address2);
        $this->iui->inputtext_label("City :", "text", "City",              $patient->City);
        $this->iui->inputtext_label("State: ", "text", "State",            $patient->State);
        $this->iui->inputtext_label("Zipcode: ", "text", "PostalCode",     $patient->PostalCode);
        $this->iui->Button_submit("Update Record");
        $this->iui->Fieldset_stop();
        $this->iui->Form_stop();

        $this->getAllMedications($id);
        $this->drawAllMedications();
        $this->getAllHerbs($id);
        $this->drawAllHerbs();
        $this->getConditions($id);
        $this->drawAllConditions();
        $this->getAllReminders($id);
        $this->drawAllReminders();
        return true;
    }

    /**
     *
     * * Destructor: Closes the connection to the database
     *
     */
      public function __destruct() {
				//$this->mysql->Close();
     }
}
?>
