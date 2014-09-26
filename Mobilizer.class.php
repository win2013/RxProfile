<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


session_start();
/**
 * Description of iui
 *
 * @author Edwin A. Hernandez, PhD
 * @property UBIWIRELESS, LLC (C)2009
 * 
 */
class IUI {

    // Variables
    private $bInitialized = false;

    //put your code here
    const ULSTART = "<ul";
    const ULEND   = "</ul>";
    const LISTART = "<li>";
    const LISTOP  = "</li>";
    const AHREF   = "<A href=\"";
    const HREF    = " href=\"";
    const A       = "</A>";
    const ID      = " id=\"";
    const TITLE   = "\" title=\"";
    const CLASSGROUP = " class =\"group\" ";
    const DIV      = "<div id=\"";
    const DIVCLOSE = "</div>";
    const _CLASS   = "\" class =\"";
    const ACLASS   = "<a class=\"";
    const BTARGET  = " href=\"#\" target=\"_self\" >";
    const FSET     = "<fieldset>";
    const FSETSTOP = "</fieldset>";
    const SELECT   = "<select>";
    const SELEND   = "</select>";
    const OPTION   = "<option ";
    const OPTEND   = "</option>";
    const FORM     = "<form ";
    const FORMEND  = "</form>";
    const LABEL    = "<label>";
    const LABELEND = "</label>";

    public function __constructor() {
        $bInitialized = true; 
    }

    public function li_start($group = false, $title)
    {
        echo self::LISTART;
        if ($group == true)
            echo self::CLASSGROUP.">";
        if ($title != null)
            echo $title.self::LISTOP;
    }

    public function li_start_ref($ref, $title)
    {
        echo self::LISTART;
        echo self::AHREF.$ref."\">";
        echo $title.self::A.self::LISTOP."\n";
    }

    public function ul_start($id, $title, $selected) {
        echo self::ULSTART.self::ID.$id.self::TITLE.$title."\"";
        if ($selected == true)
            echo " selected=\"true\" ";       
        echo "> \n";
    }

    public function ul_stop() {
       echo self::ULEND;
    }

    public function label($label) {
        echo self::LABEL.$label.self::LABELEND;
    }
    
    public function div_start($id, $class, $title) {
        if ($class != null)
            echo self::DIV.$id.self::_CLASS.$class.self::TITLE.$title."\"> \n";
        else
            echo self::DIV.$id.self::TITLE.$title."\" > \n";
    }
    
    public function div_start_row(){
        echo "<div class=\"row\" >\n";
    }

    public function div_close(){
        echo self::DIVCLOSE."\n";
    }

    public function Button($class, $type, $title) {
          echo self::ACLASS.$class."\" type=\"".$type."\"".self::BTARGET.$title."</a> \n";
    }

    public function Button_submit($label) {
         echo self::ACLASS."whiteButton\" type=\"submit\" href=\"#\" >".$label."</a> \n";
    }
    public function Button_href($class, $href, $title) {
          echo self::ACLASS.$class."\"".self::HREF.$href."\">".$title."</a> \n";
    }
   
    public function inputtext($type, $name, $value) {
       echo "<input type=\"".$type."\" name=\"".$name."\"  value=\"".$value."\" /> \n";
   }

    public function inputtext_label($label, $type, $name, $value) {
        echo "<div class=\"row\"> \n";
        echo self::LABEL.$label.self::LABELEND;
        echo "<input type=\"".$type."\" name=".$name."\"  value=\"".$value."\" /> \n";
        echo self::DIVCLOSE;
    }

    public function Fieldset_start(){
        echo self::FSET;
    }

    public function Fieldset_stop(){
       echo self::FSETSTOP;
    }

    public function Form_start($id, $class, $action, $bSelected) {
        echo self::FORM.self::ID.$id.self::_CLASS.$class."\" method=\"POST\" action=\"".$action."\" ";
        if ($bSelected == true)
            echo " selected=\"true\" ";
         echo "> \n";
    }

    public function Form_Stop(){
        echo self::FORMEND."\n";
    }

    public function Select($options) // An array of value, string pair
    {
        echo self::SELECT;
        foreach ($options as $key => $value){
            echo self::OPTION." Value=\"".$key."\">".$value.self::OPTEND;
        }
        echo self::SELEND;
    }

    public function Header($value, $header) {
        echo "<h".$value.">".$header."</h".$value.">";
    }

   public function drawHeader($title = "Title")
   {
      echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"> \n";
      echo "<html xmlns=\"http://www.w3.org/1999/xhtml\"> \n";
      echo "<head> \n";
      echo "<title>".$title."</title> \n";
      echo "<meta name=\"viewport\" content=\"width=devicewidth; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;\"/> \n";
      echo "<link rel=\"apple-touch-icon\" href=\"iui/iui-logo-touch-icon.png\" /> \n";
      echo "<meta name=\"apple-touch-fullscreen\" content=\"YES\" /> \n";
      echo "<style type=\"text/css\" media=\"screen\">@import \"iui/iui.css\";</style> \n";
      echo "<script type=\"application/x-javascript\" src=\"iui/iui.js\"></script> \n";
      echo "</head> \n";
      echo "<body>  \n";
   }

   public function drawBottom(){
     // $this->ul_start("bottom", "none", true);
     // $this->Header("3", "(c) 2009  UBIWIRELESS, LLC");
     // $this->ul_stop();
      echo "</body> \n ";
      echo "</html> \n";
   }

   public function drawToolBar() {
       echo "<div class=\"toolbar\"> \n";
       echo "<h1 id=\"pageTitle\"></h1> \n";
       echo "<a id=\"backButton\" class=\"button\" href=\"#\"></a> \n";
       echo "</div> \n";
   }


}
?>
