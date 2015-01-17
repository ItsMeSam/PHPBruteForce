<?php

require "app/bruteforce.class.php";

BruteForce::init(array(
		'url'         => 'http://127.0.0.1/PHPBruteForce/login.php', // URL from the login page.
		'username'    => 'username', // The username in the POST request.
		'password'    => 'password', // The password in the POST request.
		'adminname'   => 'admin',    // The name of the admin on the login page, in this class is brute-forcing only for the password.
		'wordlist'    => 'wordlist/wordlist.txt', // Path to the wordlist.
		'failcontent' => 'Error', // Content of page when logging in fails.
		'outputfile'  => 'output/output.txt', // Output file where the matching combo('s) will be stored in.
		'timezone'    => 'Europe/Amsterdam' // Time zone for the output file.
	));

$bf = new BruteForce(); // Instanciate the class.
echo $bf->attack(); // Use the attack function.

?>