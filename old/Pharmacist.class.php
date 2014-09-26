<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
    private $mydb;

    public function __constructor ($id = null, $db = null )
    {
       $values = $array();
       if ($db == null) {
           $mydb = new MySQL();
           if ($mydb->Error()) $db->Kill();
       }
       else
           $mydb = $db;

       if ($id != null)
            $this->$id = $id;
       else
            $this->$id = 0;
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

    public function getMyInfo($xml = true) {
        $where[PHARMID] = $id;
        $query = SEFROM.PHARMTABLE.WHERE.PHARMID." = ".$id;
        if (! $mydb->Query($query) )  $mydb->Kill();
        if ($xnl == true)
            return $mydb->GetXML();
        else
            return getMyInfoHTML();
    }

    public function setMyInfo($update)
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

        return 1;
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
