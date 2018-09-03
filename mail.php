<?php
 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/*
	$to = "betaserver981@gmail.com";
	$subject = "Testing";
	$txt = "This is testing mail....";

	if(mail($to,$subject,$txt)){
		echo "mail send successfully";
	}else{
		echo "Something went wrong";
	} */
	//phpinfo();
	$a = null;

print $a ?? 'b';
print "\n";

print $a ?: 'b';
print "\n";

print $c ?? 'a';
print "\n";

print $c ?: 'a';
print "\n";
?>