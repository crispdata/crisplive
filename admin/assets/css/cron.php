<?php
$to = "sharmasuraj41@gmail.com"; 
$fr_email = 'info@crispdata.co.in'; 
$subj = 'Hi this is from AWS Cron';
/* Create a simple msg body */
$body = "Welcome to Aws\n";
$body .= "\n";
// Now send email
mail($to,  $subj, $body, "From: <$fr_email>");
?>