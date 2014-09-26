<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
/**
 * Description of Pharmacist
 *
 * @author Edwin A Hernandez
 * @@copyright UBIWIRELESS (c)2009
 *
 */

class Pharmacist {
    //put your code here
    private $values;

    const SEFROM     = "SELECT * FROM ";
    const INSERT     = "INSERT INTO ";
    const UPDATE     = "UPDATE ";
    const WHERE      = " WHERE ";
    const PHARMTABLE = "Pharmacists";
    const PHARMID    = "id";
    const PATTABLE   = "Patient_Demographics";
    const PATCOL     = "Pharmacist";

    // MySQL content from mysql.class file
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

    public function getMyInfoHTML(){
        $result = "";
        $mydb->MoveFirst();
        while (! $db->EndOfSeek()) {
            $row =  $mydb->Row();
            $post = $mydb->SeekPosition() ;
            echo "<h2>". $row->Color . " and " . $row->Age . "<br />\n";
        }
        return $resuilt;
    }

     public function getMyInfo() {
        $where[PHARMID] = $id;
        $query = SEFROM.PHARMTABLE.WHERE.PHARMID." = ".$id;
        return $this->doQuery($query);
    }
    

    public function setMyInfo($id, $update)
    {
        // $update is an associative arry with the data to update
        $where[PHARMID] = MySQL::SQLValue($id, "integer");
        $result = $mydb->UpdateRows(PHARMTABLE, $update, $where);
        if (! $result) {
             // Show the error and kill the script
           $db->Kill();
        }
    }

    public function delete()
    {
        $where[PHARMID] = $id;
        $mydb->DeleteRows(PHARMTABLE, $where);
    }

    public function getAllPatients()
    {
        $query = SEFROM.PATTABLE.WHERE.PATCOL." = ".$id;
        if (!$mydb->query())
           $mydb->dbKill();
       return $mydb->GetXML();
    }


    public function drawPharamacist()
    {

        $this->getMyInfo();
        $pharmacist = $this->mydb->Row();
        //print_r($patient);
        $id = $Pharmacist->MedicaidID;
        $this->iui->ul_start("home", "Rx Profile", true);
        $this->iui->li_start_ref("#Pharmacist", "Pharmacist Profile");
        //$this->iui->li_start_ref("index.php?screen=AddMedication&id=".$id, "Add New Medication");
        $this->iui->Button_href("whiteButton", "index.php?screen=showMedications&id=".$id, "Manage Medications");
        $this->iui->Button_href("whiteButton", "index.php?screen=showHerbals&id=".$id, "Manage Herbal Sup.");
      
        $this->iui->ul_stop();
        $this->iui->Form_start("Pharmacist", "panel", "index.php?screen=UpdatePharmacist&id=".$id, false);
        //$this->iui->Form_start("Patient", "panel", "getPost.php?id=".$id, false);

        $this->iui->Header("2", "Rx: Pharmacist Profile");
        $this->iui->Fieldset_start();
        $this->iui->label("Medicaid ID: ".$pharmacist->id);
        $this->iui->inputtext_label("First Name: ",  "text", "firstName",  $pharmacist->firstName);
        $this->iui->inputtext_label("Last Name: ",   "text", "lastName",   $pharmacist->lastName);
        $this->iui->inputtext_label("Address :", "text", "Address",        $pharmacist->Address);
        $this->iui->inputtext_label("Apt/Other :","text", "Address2",      $pharmacist->Address2);
        $this->iui->inputtext_label("Title :", "text", "Title",            $pharmacist->Title);
        $this->iui->inputtext_label("Position: ", "text", "Position",      $pharmacist->Position);
        $this->iui->inputtext_label("E-mail: ", "text", "email",           $pharmacist->email);
        $this->iui->Button_submit("Update Record");
        $this->iui->Fieldset_stop();
        $this->iui->Form_stop();

        return true;
    }


    /**
     *
     * * Destructor: Closes the connection to the database
     *
     */
      public function __destruct() {
	//$this->Close();
    }
}
?>
