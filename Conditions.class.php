<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();

/**
 *
 * Description of Conditions
 *
 * @author Edwin A. Hernandez
 * @property UBIWIRELESS, LLC (c) 2009
 *
 */

class Conditions {
    //put your code here
    private $mydb;
    private $iui;
    private $login;
    private $bPatient = false;

    const SEFROM     = "SELECT * FROM ";
    const MEDTABLE   = "Medication";
    const WHERE      = " Where ";

    public function initialize($login, $db, $iui )
    {
        $this->iui  = $iui;
        $this->mydb = $db;
        if ($_SESSION[self::PATIENTKEY]!="None")
            $this->bPatient = true;
        $this->login = $login;
    }
   
    public function drawThis(){
       
    }

}
?>