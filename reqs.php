<?php
require './vendor/autoload.php';
require "./secret.php";
use React\Socket\ConnectionInterface;
use NewTwitchApi\HelixGuzzleClient;
use NewTwitchApi\NewTwitchApi;

// classes

class Twitch {
    public $connection, $secret, $nick, $broadcaster;


    function __construct($secret, $nick, $broadcaster){
        $this->secret = $secret;
        $this->nick = $nick;
        $this->broadcaster = $broadcaster;
    }
    public function setConnection(ConnectionInterface $connection){
        $this->connection = $connection;
    }
    
    public function initIRC(){
        $this->connection->write("PASS " . $this->secret . "\n");
        $this->connection->write("NICK " . $this->nick . "\n");
        $this->connection->write("CAP REQ :twitch.tv/tags\n");
        $this->connection->write("JOIN #" . $this->broadcaster . "\n");
    }

    public function pingPong($data, $output){
        $output->write("[" . date('h:i:s') . "] $data\n");
        $this->connection->write(preg_replace('/PING/', 'PONG', $data));
        $output->write("[" . date('h:i:s') . "]" . preg_replace('/PING/', 'PONG', $data) . "\n");
    }

    public function sendMessage($data){
        $payload = "PRIVMSG #" . $this->broadcaster . " :" . $data . "\n";
        $this->connection->write($payload);
    }
}
class User {
    public $isSub, $isMod, $isBroadcaster, $name, $id;

    function __construct($data, $broadcaster){
        $rawarray = explode(';', $data);
        foreach ($rawarray as $i){
            $newentry = explode('=', $i);
            $acceptedTags = array(
                'display-name',
                'id',
                'mod',
                'subscriber',
                'tmi-sent-ts',
                'user-id'
            );
            if (in_array($newentry[0], $acceptedTags)){
                $userArray[$newentry[0]] = $newentry[1];
            }
        }  

        if($userArray['display-name'] == $broadcaster){
            $this->isBroadcaster = true;
        }
        else {
            $this->isBroadcaster = false;
        }
        $this->isSub = boolval($userArray['subscriber']);
        $this->isMod = boolval($userArray['mod']);
        $this->name = $userArray['display-name'];
        $this->id = $userArray['user-id'];
    }
}
class Message {
    public $message;
    public $isCommand;
    public $isPing;
    public $command;
    public $args;
    private $user;

    function __construct($data, $userObj){    
        include "./secret.php";    
        if (preg_match('/^PING/', $data)){
            $this->isPing = true;
        }
        else {
            $this->user = $userObj;
            $pattern = "/(.*)" . $broadcaster . " \:/";
            $this->message = preg_replace($pattern, '', $data);
            if ($this->message[0] == "!"){
                $this->isCommand = true;
                $this->message = substr($this->message, 1);
                $full = explode(' ', $this->message);
                $this->command = array_shift($full);
                $this->args = $full;
            }
            else {
                $this->isCommand = false;
            }
        }
    }
    public function execute($conn, $user){
        $query = "SELECT * FROM commands WHERE input='".trim($this->command)."'";
        $response = mysqli_fetch_assoc($conn->query($query));
        if (!isset($response)){
            return false;
        }
       
        //check execution conditions
        
        $live = (isLive() ? true : false);
        /* 
        Uncomment line below to make bot always active.
        Increases response time, but...well then the bot is always active.
        */
        //$live = true;
        $available = (time() > $response['last_used'] + $response['cooldown'] ? true : false);
        $restricted = (boolval($response['mod_only']) ? true : false);
        if ($live){
            if (!$restricted){
                if ((boolval($user->isMod)) || (boolval($user->isBroadcaster)) || ($available)){
                    $execute = true;
                }
                else {
                    $execute = false;
                }
            }
            else {
                if ((boolval($user->isMod)) || (boolval($user->isBroadcaster))){
                    $execute = true;
                }
                else {
                    $execute = false;
                }
            }
        }
        else {
            if (boolval($user->isBroadcaster)){
                $execute = true;
            }
            else {
                $execute = false;
            }
        }

        if ($execute){
            if ($response['output'] == "function"){
                $file = './functions/' . trim($this->command) . ".php";
                //ob_start();
                include $file;
                $output = ob_get_clean();
                $update = "UPDATE commands SET last_used=" . time() . " WHERE input='".trim($this->command)."'";
                //$conn->query($update);
                //return $output;
            }
            else {
                $update = "UPDATE commands SET last_used=" . time() . " WHERE input='".trim($this->command)."'";
                $conn->query($update);
                return $response['output'];
            }

        }
    }
}

// utilities

function isLive(){
    include "./secret.php";
    $twitch_client_id = $client_id;
    $twitch_client_secret = $client_secret;
    $twitch_scopes = '';

    $helixGuzzleClient = new HelixGuzzleClient($twitch_client_id);
    $twitchApi = new NewTwitchApi($helixGuzzleClient, $twitch_client_id, $twitch_client_secret);
    $oauth = $twitchApi->getOauthApi();
    try {
        $token = $oauth->getAppAccessToken($twitch_scopes ?? '');
        $data = json_decode($token->getBody()->getContents());
        // Your bearer token
        $twitch_access_token = $data->access_token ?? null;
    } catch (Exception $e) {}

    $response = $twitchApi->getStreamsApi()->getStreamForUsername($twitch_access_token, $broadcaster);
    $responseContent = json_decode($response->getBody()->getContents(), true);
    if (isset($responseContent->data[0])){
        return true;
    }
    else {
        return false;
    }
}