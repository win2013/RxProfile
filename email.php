<?php
$to="5613064996@txt.att.net";
$subject = "Hi!";
$body = "Hi,\n\nHow are you?";
if (mail($to, $subject, $body)) {
  echo("<p>Message successfully sent!</p>");
 } else {
  echo("<p>Message delivery failed...</p>");
 }
?>
