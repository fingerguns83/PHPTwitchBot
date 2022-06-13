<?php

// Init Resources
use React\EventLoop\Factory;
use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

require 'reqs.php';


// establish classes
$twitch = new Twitch($secret, $nick, $broadcaster);
$loop = Loop::get();
$connector = new Connector($loop);
$input = new ReadableResourceStream(STDIN, $loop);
$output = new WritableResourceStream(STDOUT, $loop);

// start
$connector->connect('irc.chat.twitch.tv:6667')->then(
    function (ConnectionInterface $connection) use ($twitch, $input, $output, $broadcaster, $sqlconn, $loop){
        $twitch->setConnection($connection);
        $twitch->initIRC();
        $connection->on('data', function($data) use ($connection, $twitch, $output, $broadcaster, $sqlconn){
            
            /* Uncomment line below for raw message output to console */
            //$output->write($data);
            isLive();

            if ($data[0] == "@"){
                $user = new User($data, $broadcaster);
            }
            else {
                $user = null;
            }

            $message = new Message($data, $user);

            if ($message->isCommand){
                $result = $message->execute($sqlconn, $user);
                if ($result){
                    $twitch->sendMessage($result, $connection);
                }
                //$output->write($data);
            }
            if ($message->isPing){
                $twitch->pingPong($data, $output, $connection);
            }
        });

        $input->on('data', function($data) use ($connection, $twitch){
            //$connection->write($data);
            $twitch->sendMessage($data, $connection);
        });
    }
);

$loop->run();