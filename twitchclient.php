<?php

use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

require 'vendor/autoload.php';
require 'twitchfunctions.php';

$loop = Factory::create();

/* Get your Chat OAuth Password here => https://twitchapps.com/tmi/ */
$secret = '';
/* Your Twitch username */
$nick = '';


$twitch = new Twitch($secret, $nick);

$connector = new Connector($loop);
$connector->connect('irc.chat.twitch.tv:6667')
->then(
    function (ConnectionInterface $connection) use ($twitch){
        $twitch->initIRC($connection);

        $connection->on('data', function($data) use ($connection, $twitch){
            $twitch->scrape($data, $connection);
        });
    },
    function (Exception $exception){
        echo $exception->getMessage() . PHP_EOL;
    }
);

$loop->run();