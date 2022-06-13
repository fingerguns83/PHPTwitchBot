<?php

$hostname = '';
$username = '';
$password = '';

//$sql = new mysqli('<host>', '<user>', '<password>');
$sql = new mysqli($hostname, $username, $password);

if ($sql->connect_error){
  die("Connection error: " . $sql->connect_error);
}

$stmt = "CREATE DATABASE PHPTwitchBot";
if ($sql->query($stmt) === true){
  echo "Database 'PHPTwitchBot' created." . PHP_EOL;
}
else {
  die("Error: " . $sql->error);
}
$sql->close();

$sql = new mysqli($hostname, $username, $password, 'PHPTwitchBot');
if ($sql->connect_error){
  die("Re-connection error: " . $sql->connect_error);
}

$stmt = "CREATE TABLE commands (
  input TEXT NOT NULL,
  output TEXT NOT NULL,
  mod_only INT(11) DEFAULT 0 NOT NULL,
  cooldown INT(32) DEFAULT 600 NOT NULL,
  last_used INT(32) DEFAULT 0 NOT NULL
  )";

if ($sql->query($stmt) === true){
  echo "Table 'commands' created." . PHP_EOL;
}
else {
  die("Error: " . $sql->error);
}
$stmt = "INSERT INTO commands (input, output) VALUES ('commands', 'function')";
if ($sql->query($stmt) === true){
  echo "Command 'commands' registered." . PHP_EOL;
}
else {
  die("Error: " . $sql->error);
}

$stmt = "INSERT INTO commands (input, output) VALUES ('8ball', 'function')";
if ($sql->query($stmt) === true){
  echo "Command '8ball' registered." . PHP_EOL;
}
else {
  die("Error: " . $sql->error);
}

$stmt = "INSERT INTO commands (input, output, mod_only) VALUES ('so', 'function', 1)";
if ($sql->query($stmt) === true){
  echo "Command 'so' registered." . PHP_EOL;
}
else {
  die("Error: " . $sql->error);
}

$stmt = "INSERT INTO commands (input, output) VALUES ('uptime', 'function')";
if ($sql->query($stmt) === true){
  echo "Command 'uptime' registered." . PHP_EOL;
}
else {
  die("Error: " . $sql->error);
}