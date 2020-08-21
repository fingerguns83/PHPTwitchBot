<?php

use React\Socket\ConnectionInterface;
require "responses.php";

function cleanup($str){
    if ($str){
        $cleaned = strtolower(trim($str));
        return $cleaned;
    }
    else {
        return null;
    }
}

class twitch {
    protected $connection;
    private $secret;
    private $nick;

    function __construct($secret, $nick){
        $this->secret = $secret;
        $this->nick = $nick;
    }
    
    public function initIRC(ConnectionInterface $connection){
        global $secret;
        $connection->write("PASS " . $this->secret . "\n");
        $connection->write("NICK " . $this->nick . "\n");
        $connection->write("CAP REQ :twitch.tv/membership\n");
        $connection->write("JOIN #" . $this->nick . "\n");
    }

    public function pingPong($data, ConnectionInterface $connection){
        echo "[" . date('h:i:s') . "] PING :tmi.twitch.tv\n";
        $connection->write("PONG :tmi.twitch.tv\n");
        echo "[" . date('h:i:s') . "] PONG :tmi.twitch.tv\n";
    }

    public function sendMessage($data, ConnectionInterface $connection){
        $connection->write("PRIVMSG #" . $this->nick . " :" . $data . "\n");
    }

    public function parseUser($data){
        global $nick;
        $messageContents = preg_replace('/.* PRIVMSG.*:/', '', $data);
        if (substr($messageContents, 0, 1) == "!"){
            $tmp = explode('!', $data);
            $user = str_replace(':', '', $tmp[0]);
            $user = "@" . $user . " - ";
            return $user;
        }
    }

    public function parseMessage($data){
        global $responseArr;
        $messageContents = preg_replace('/.* PRIVMSG.*:/', '', $data);
        $dataArr = explode(' ', $messageContents);

        $key = cleanup($dataArr[0]);
        /* $arg1 = cleanup($dataArr[1]); Just kind of future-proofing here */

        if (isset($responseArr[$key])){
            return $responseArr[$key];
        }
        else {
            $error = "Unknown command. Valid commands are:";
            foreach ($responseArr as $i => $j){
                $error .= " '$i',";
            }
            $error = substr($error, 0, -1);
            return $error;
        }
    }

    public function scrape($data, ConnectionInterface $connection){
        if (trim($data) == "PING :tmi.twitch.tv"){
            $this->pingPong($data, $connection);
            return;
        }
        if (preg_match('/PRIVMSG/', $data)){
            $response = $this->parseMessage($data);
            if ($response){
                $user = $this->parseUser($data);
                $payload = $user . $response . "\n";
                $this->sendMessage($payload, $connection);
            }
        }
    }
}