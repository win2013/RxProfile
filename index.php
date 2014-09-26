<?php
    include ("RxProfile.class.php");

    session_start();

    $id      = $_REQUEST['id'];				// Two main variables $id and $screen
    $screen  = $_REQUEST['screen'];
    $nscreen = $_REQUEST['nextscreen'];

    if ($screen == null ) $screen = "init";
    else
      {
       //   print_r($_REQUEST);
       //  print_r($_POST);
      //  print_r($_GET);
      }
    $rxProfile = new RXProfile();
    $iui       = new IUI();
    $db        = new MySQL(true, RXProfile::DBASE, RXProfile::HOST, RXProfile::USERNAME, RXProfile::PASSWORD);

    $rxProfile->initialize($iui, $db);
 
    if ( ($screen === 'authenticate') && ($_SESSION['login'] == false) )
    {
        $login          = $_REQUEST['login'];
        $password       = $_REQUEST['password'];
        $type           = $_REQUEST['type'];
        if ($type == 'Patient') {
           $auth = $rxProfile->authenticate(true, $login, $password);
           $screen = "getPatient";
           if ($nscreen == "getReminder") {
               $screen = $nscreen;
           }
        }
        else
        {
           $auth = $rxProfile->authenticate(false,  $login, $password);
           $screen = "getPharmacist";
        }
        if (($auth == false) && ($screen == 'authenticate'))
           $screen = 'init';
    } 
    else
    {
        $login   = $_REQUEST["login"];
        if ($screen == 'authenticate')
            $screen = "get".$_REQUEST['type'];          
    }

    if ($screen == "init") {
    	  /*  if ($_SESSION['login']==true){ 
    	   	   if ($_SESSION['patient']!= 'None') { 
    	   	        $screen = 'getPatient';
    	   	        $login  = $_SESSION['patient']; 
    	   	   }
    	       if ($_SESSION['pharmacist'] !='None') {
    	       	    $screen = 'getPharmacist';
    	       	    $login = $_SESSION['pharmacist'];
    	       } 
    	    } */
         $rxProfile->drawTop(); 
     }

    switch ($screen) {
        case "init":
            $rxProfile->drawInit();
            break;        
        case "PharmacistLogin":
            $rxProfile->drawLogin(false, false);
            break;
        case "PatientLogin":
            $rxProfile->drawLogin(true, false);
            break;
        case "showMedications":
            if ($_SESSION['login']==true)
               $rxProfile->drawMedications($login);
            break;
        case "showHerbals":
            if ($_SESSION['login']==true)
               $rxProfile->drawHerbals($login);
            break;
        case "getMedication":
             if ($_SESSION['login']==true)
                $rxProfile->drawMedication($login, $_GET['id'] );
             break;
        case "getReminder":
            if ($_SESSION['login']==true)
            {
                $rxProfile->drawTop();
                $rxProfile->drawReminder($login, $_GET['ReminderID'], $_GET['DrugName'] );
            }
            else {
                $_SESSION['ReminderID'] = $_GET['ReminderID'];
                $_SESSION['DrugName']   = $_GET['DrugName'];
                $rxProfile->drawTop();
                $rxProfile->drawLogin(true, true);  //getReminder Screen is followed.
            }
            break;
        case "getPatient":
            if ($_SESSION['login']==true)
                $rxProfile->drawPatient($login);
            break;
        case "getPharmacist":
            if ($_SESSION['login']==true)
                $rxProfile->drawPharmacist($login);
            break;        	
        case "deleteMedication":
            if ($_SESSION['login'] == true)
                 $rxProfile->deleteData("Medication", $_GET['id'], '0' );
            break;
        case "deletePatientMedication":
            //echo "Deleting.... "; 
            if ($_SESSION['login'] == true)
                $rxProfile->deleteData("PatientMedication", $_GET['patientid'], $_GET['id'] );
            break;
        case "addMedicationToPatient":
        case "updateMedicationToPatient":
            if ($_SESSION['login'] == true) 
                $rxProfile->updateData("MedicationToPatient", $_GET['id'], $_POST);
           break;  
        case "addMedication":    
        case "updateMedication":
           if ($_SESSION['login'] == true) 
           	 $rxProfile->updateData("Medication", $_GET['id'], $_POST);
        	 break;  
        case "addPatient":       
        case "UpdatePatient":
            if ($_SESSION['login'] == true) { 
                $rxProfile->updateData("Patient", $_GET['id'],   $_POST);
             }
            break;
        case "Exit":
            echo "Exit!";
            break;
    }
    $rxProfile->drawBottom();
    
   // function addQuotes($values) 
   // {
   // 		foreach ($values as $k => $v) {
   //				$values[$k] = "'".$v."'";
   //     }
   //      return $values;
   //  }
?>     
