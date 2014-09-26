
<ul title="Honduras">
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//Just for testing.

   echo '<li> <a href="#post">'. REQUEST. '</a>';
?>
</ul>
<div id="post" title="POST" class="panel">
     <h2>POST data</h2>
     <ul>
     <li><a href="#">
     <?php
        print_r($_REQUEST);
     ?>
       </a> 
       </li>     
        <li><a href="#">
        <?php
           print_r($_POST);
        ?>
       </a> 
       </li>
        <li><a href="#">
        <?php
           print_r($_GET);
        ?>
       </a>
       </li>
    </ul>
</div>
