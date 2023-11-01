<?php

//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('1034286712318-kv0gapfqro1aijq84ed72r4aqqs8nan8.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('Dzq5Xd3olizoZkKjk_SJCWQ1');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri('http://localhost/tutorial/php-login-using-google-demo/index.php');

//
$google_client->addScope('email');

$google_client->addScope('profile');

//start session on web page
session_start();

?>