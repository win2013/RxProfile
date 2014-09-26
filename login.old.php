<?		
session_start();

header("Content-type: text/xml");
################################################################################
# params
$login = $_POST["login"];
$password = $_POST["password"];
$conn  = connect();

# Not used here: let you know that the request must be considered as an
# asynchronous request and render a XML content instead of XHTML (more
# information about this will be available with the WebApp PHP framework)

$async = ($_GET["__async"] == "true");

################################################################################
# Rendering

$person = array (
	"MedicaidID" => "",
	"FirstName" => "",
	"LastName" => "",
	"Address"=>"",
    "Address2"=>"",
 	"City"=>"",
	"State"=>"", 
    "PostalCode"=>""
);

function Draw() {
	global $login, $password, $person;
	if (($login=="") || ($password == ""))
	{
		     echo "<div class=\"err\">You must enter a password</div>";
	}
	else {
		$SESSION["logged_in"] = false;
		$res = sql_select();
		if ($res != "") 
		{	
			$_SESSION["logged_in"] = true;				
		    echo "<div class=\"msg\">Welcome MedicaID# $login</div>";
		    getperson();	
		   // echo "<div class=\"iLayer\" id=\"waLists\" title=\"Lists Demo\">"; 
?>
				<a href="#" rel="back" class="iButton iBClassic" onclick="return WA.Back()">Back</a> 
				<div class="iBlock" id="list0"> 
				<h1>About myself!</h1> 
				<p>My name is <?php echo $person['FirstName']." ".$person["LastName"]; ?> and live at 
				<?php echo $person["Address"]." ".$person["Address2"].", ".$person["City"].", ".$person["State"].", ".$person["PostalCode"]; ?>
				</p> 
				</div> 
				
				<div class="iMenu"> 
					<ul class="iArrow"> 
					<li><a href="#_Medications">My Medications</a></li> 
					<li><a href="#_Conditions">My Conditions</a></li>
					<li><a href="#_Hearbal">My Herbal Supplements</a></li>
					<li><a href="$_Appointments">My Appointments</a></li>					
					</ul> 
					<ul> 
						<li style="text-align:center"><a href="getUpdates.php" rev="async">Check for new updates?</a></li> 
					</ul> 
				</div>		
		
 
<?php 			    
		} 
		else
		{
  		     echo "<div class=\"err\">Wrong or incorrect usernamer/password pair </div>";
  
		}
	}	
 }
################################################################################
################################################################################
?>

<?php 
function connect()
{
  $conn = mysql_connect("192.168.0.199", "ufmed", "Gators2009");
  mysql_select_db("ufpdt");
  return $conn;
}

function getperson(){
	global $conn;
	global $person;
	global $login;	
	
	$sql = "SELECT `Medicaid ID`, FirstName, LastName, Address, Address2, City, State, PostalCode From Patient_Demographics where `Medicaid ID` = '".$login."'";
	$res = mysql_query($sql, $conn);
	$person = mysql_fetch_assoc($res);
    return $person;
}

function getMedications(){
		
}

function getMedicationInfo(){
	
}

function getConditions(){	
	
}

function getHerbal(){
	
}

function sql_select()
{
  global $conn;  	
  global $login;
  global $password; 
  $sql = "SELECT * FROM usernames Where medicaidid = '".$login ."' and PIN = '".$password."'";
  //echo $sql;
  $res = mysql_query($sql, $conn);// or die(mysql_error());  
  return $res;
}

?>

<root>
	<destination mode="replace" zone="<? echo ($password == "") ? "form-res" : "waHome"; ?>" />
	<data><![CDATA[ <? echo Draw() ?> ]]></data>
</root>

<?php 
   mysql_close($conn);
?>