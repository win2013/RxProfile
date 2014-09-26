<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
/**
 * Description of Reminders
 *
 * @author edwin
 */
class Reminders {

       private $db;
       private $id;
       private $iui;
       private $login;
       private $drugName;

       public function initialize($login, $id, $db, $iui){
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

       public function setDrugName($name) {
            $this->drugName = $name;
       }

       public function drawThis() {
             $this->iui->ul_start("home", "Rx:".$Medication->Drug, true);
             $this->iui->Header("2","Adherence to Medication :".$this->drugName);
             //$this->iui->div_start("Remindersid".$this->id, "panel", $this->id);
             $direct = 'Please provide some feedback to RxProfile, Have you taken this medication?';
             $this->iui->Header("2", $direct);
             $this->iui->Button_href("whiteButton", "index.php?screen=getFeedback&id=".$this->id, "Yes");
             $this->iui->Button_href("redButton",   "index.php?screen=getFeedback&id=".$this->id, "No" );
            // $this->iui->div_close();
             $this->iui->ul_stop();
       }
}


?>
