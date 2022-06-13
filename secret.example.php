<?php

// Retrieve your twitch IRC token here: https://twitchapps.com/tmi/
$secret = "oauth:"; 

// Name of your bot account
$nick = "<bot display name>";

// Twitch Application Info: https://dev.twitch.tv/console
$client_id = '<app client id>';
$client_secret = '<app client secret>';

//connection to your database
$hostname = '<host>';
$username = '<user>';
$password = '<password>';

// Name of the channel the bot will be active on
$broadcaster = '<channel display name>';



/* leave this line alone */
$sqlconn = new mysqli($hostname, $username, $password, 'PHPTwitchBot');
?>