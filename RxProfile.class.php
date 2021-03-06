<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//this is db connection file and has nothing to do withe the class itself 
require("mysql.class.php");
require("Medication.class.php");
require("Medications.class.php");
require("Patient.class.php");
require("Pharmacist.class.php");
require("IUI.class.php");
require("Reminders.class.php");

session_start();

/**
 * Description of RxProfile
 * The whole application, including a Pharmacist is picked to handle a set of Patients
 * Which indeed use the system
 *
 * @author Edwin A Hernandez
 * @copyright UBIWIRELESS (c)2009
 * 
 */

class RxProfile {

   const DBASE      = "ufpdt";
   const USERNAME   = "ufmed";
   const PASSWORD   = "Gators2009";
   const HOST       = "192.168.0.199";

   const SEFROM     = "SELECT * FROM ";
   const INSERT     = "INSERT INTO ";
   const WHERE      = " WHERE ";

   //DB Tables
   const USERTABLE      = "usernames";
   const PHARMTABLE     = "Pharmacists";
   const MEDICTABLE     = "Medication";
   const MEDICDIRTABLE  = "Medication_Directions"; 

   // Key to search SESSION value
   const PATIENTKEY   = "Patient";

   const APPNAME      = "RxProfile";

   private  $db;
   private  $iui;

   private $phar;
   private $patient;
   private $medication;
   private $Reminder;

   public function __constructor () {      
   }

   public function initialize($iui, $db) {
     $this->iui = $iui;
     $this->db  = $db;
  }

   public function drawTop()
   {              
       $this->iui->drawHeader(self::APPNAME);
       $this->iui->drawToolBar();
   }
   
   public function drawBottom()
   {
       $this->iui->drawBottom();
   }

   public function drawLogin($bPatient = false, $reminder = false){
   	    //$_SESSION['login']
        $this->iui->drawImage("http://rxprofile.net/rxprofile.png", 200, 52);
   
        $this->iui->Form_start("Login", "panel", "index.php", true);
        if ($bPatient == true)
            $this->iui->Header("2", "Rx Profile Patient ");
        else
            $this->iui->Header("2", "Rx Profile Pharmacist ");
        $this->iui->Fieldset_start();
        $this->iui->div_start_row();
        if ($bPatient == true) {
            $pName = self::PATIENTKEY ;
            $_SESSION[PATIENTKEY] = true;
            $this->iui->label("Medicaid ID:");
        }  
        else
        {
            $pName = "Pharmacists";
            $_SESSION[self::PATIENTKEY] = false;
            $this->iui->label("ID:");
        }
        $this->iui->inputtext("text", "login", "");
        $this->iui->div_close();
        $this->iui->div_start_row();
        $this->iui->label("PIN :");
        $this->iui->inputtext("text", "password", "");
        $this->iui->inputtext("hidden", "type", $pName);
        $this->iui->inputtext("hidden", "screen", "authenticate");
        if ($reminder==true)
            $this->iui->inputtext("hidden", "nextscreen", "getReminder");
        $this->iui->div_close();       
        $this->iui->Button_submit("Login");
        $this->iui->Fieldset_stop();
        $this->iui->Form_stop();
   }

   public function drawInit(){
       //$this->iui = new IUI();
       //$this->iui->div_start("Login", "panel", "Login");
       $this->iui->ul_start("Start", "Login", true);
       $this->iui->Header("2", "RxProfile :: by UBIWIRELESS LLC / EGLA CORP");
       $this->iui->drawImage("http://rxprofile.net/rxprofile.png", 200, 52);
       $this->iui->Header("3" ,"(c) 2010  alpha-build for the University of Florida ");
       $this->iui->Button_href("whiteButton", "index.php?screen=PatientLogin", "Patient Login" );
       $this->iui->Button_href("whiteButton", "index.php?screen=PharmacistLogin", "Pharmacists Login");
       $this->iui->ul_stop();
       //$this->iui->div_close();
  }

   public function drawPatient($id){
       $this->patient = new Patient();
       $this->patient->initialize($id, $this->db, $this->iui);
       $this->patient->drawPatient();
       return $this->patient;
   }

   public function drawPharmacist($id) {
      $this->pharmacist = new Pharmacist();
      $this->pharmacist->initialize($id, $this->db, $this->iui);
      //$this->pharmacist->drawPharmacist();  
      return $this->pharmacist;
   }

   public function drawMedications($login){
       $this->meds = new Medications();
       $this->meds->initialize($login, $this->db, $this->iui);
       $this->meds->drawThis();
       return $this->meds;
   }

   public function drawMedication($login, $id) {
       $this->drug = new Medication();
       $this->drug->initialize($login, $id, $this->db, $this->iui);
       $this->drug->drawThis($this->bPatientm, $sType);
       return $this->drug;
   }

   public function drawReminder($login, $id, $drugName){
       $this->Reminders = new Reminders();
       $this->Reminders->initialize($login, $id, $this->db, $this->iui);
       $this->Reminders->setDrugName($drugName);
       $this->Reminders->drawThis();
       return $this->Reminders;

   }

   public function updateData($sType, $id, $update) {
       // echo "Updating data.... ".$sType; 
       // print_r($update);
      
       switch ($sType ) {
           case "Patient": 
               $patient = $this->drawPatient($id); 
               $patient->setMyInfo($id, $update);
               $patient->drawPatient();
               break;
           case "Medication":
               $medication = $this->drawMedication($id); 
               $medication->setMyInfo($id, $update);
               break;
           case "Pharmacist":
           	   $pharmacist = $this->drawPharmacist($id);
               $pharmacist->setMyInfo($id, $update);
               break;
           case "MedicationToPatient":
               $patient = $this->drawPatient($id);  
               $patient->insertintoMedication($update);
               $patient->drawPatient();
               break;                  
           //case "Conditions":
           //    $this->Condition->setMyInfo($update);
           //    break;
            
       }       
       
             
   }      
    
   public function deleteData($sType, $id1, $id2) {       
       switch ($sType) {
           case "Patient": 
               $patient = $this->drawPatient($id1);   
               $patient->delete($id1);
               break;
           case "PatientMedication":
               $patient = $this->drawPatient($id1);
               $patient->deletePatientMedication($id1, $id2);
               break;
           case "Medication":
           	   $this->Medication->delete($id1);
           	   break;    
           case "Pharmacist":
               $this->pharmacist->delete($id1);
               break;
           case "Conditions":
               $this->Condition->delete($id1);
               break;
       }             
   }       

   public function authenticate($patient, $login, $password){
	 if ($patient == true)
        {
           $sql = self::SEFROM.self::USERTABLE.self::WHERE." medicaidid = '".$login ."' and PIN = '".$password."'";
           $_SESSION['patient']     = $login;
           $_SESSION['pharmacist']  = "None";
        }
        else
        {
           $sql = self::SEFROM.self::PHARMTABLE.self::WHERE." id = '".$login."' and PIN ='".$password."'";
           $_SESSION['patient']     = "None";
           $_SESSION['pharmacist']  = $login; 
        }

        $res = $this->db->Query($sql);
        echo "Rows from Query : ".$this->db->RowCount();
        if ($res != null)
            $_SESSION['login'] = true;
        else            
            $_SESSION['login'] = false;

        return $_SESSION['login'];
   }
}

?>
