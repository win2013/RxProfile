<?php
    include ("RxProfile.class.php");

    session_start();

    $id     = $_REQUEST['id'];
    $screen = $_REQUEST['screen'];
    if ($screen == null ) $screen = "init";
    else
      {
     //   print_r($_REQUEST);
     //   print_r($_POST);
     //   print_r($_GET);
      }
    $rxProfile = new RXProfile();
    $iui       = new IUI();
    $db        = new MySQL(true, RXProfile::DBASE, RXProfile::HOST, RXProfile::USERNAME, RXProfile::PASSWORD);

    $rxProfile->initialize($iui, $db);
 
    if (($screen === 'authenticate') && ($_SESSION['login'] == false) )
    {
        $login          = $_REQUEST['login'];
        $password       = $_REQUEST['password'];
        $type           = $_REQUEST['type'];
        if ($type == 'Patient') {
           $auth = $rxProfile->authenticate(true, $login, $password);
           $screen = "getPatient";
        }
        else
        {
           $auth = $rxProfile->authenticate(false,  $login, $password);
           $screen = "getPharmacist";
        }
        if ($auth == false)
           $screen = 'init';
    } else
    {
        $login   = $_REQUEST['login'];
	if ($screen == 'authenticate') 
            $screen = "get".$_REQUEST['type'];
    }

    if ($screen =="init")
        $rxProfile->drawTop();

    switch ($screen) {
        case "init":
            $rxProfile->drawInit();
            break;        
        case "PharmacistLogin":
            $rxProfile->drawLogin(false);
            break;
        case "PatientLogin":
            $rxProfile->drawLogin(true);
            break;
        case "AddMedication":
        case "showMedications":
            $rxProfile->drawMedications($login);
            break;
        case "getMedication":
            $rxProfile->drawMedication($login, $_GET['id'] );
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
            break;
        case "UpdatePatient":

            if ($_SESSION['login']==true)
               $rxProfile->updatePatient($login, $_POST);
            break;
        case "Exit":
            echo "Exit!";
            break;
    }
    $rxProfile->drawBottom();

?>     
