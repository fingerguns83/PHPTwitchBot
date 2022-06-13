<?php
include './secret.php';
use NewTwitchApi\HelixGuzzleClient;
use NewTwitchApi\NewTwitchApi;

if ($this->args[0]){
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
    $response = $twitchApi->getUsersApi()->getUserByUsername($twitch_access_token, trim($this->args[0]));
    $responseContent = json_decode($response->getBody()->getContents(), true);
    
    if (isset($responseContent['data'][0])){
        $formattedName = $responseContent['data'][0]['display_name'];
        $messages = array(
            "Go follow $formattedName! Seriously. Right now. Go follow them! https://twitch.tv/$formattedName",
            "HEY! Check out $formattedName! https://twitch.tv/$formattedName",
            "Click 👏 the 👏 link 👏 and follow $formattedName! https://twitch.tv/$formattedName",
            "🚨THIS IS NOT A DRILL🚨 Go drop $formattedName a follow. https://twitch.tv/$formattedName"
        );
        shuffle($messages);
        echo $messages[0];
    }
    else {
        echo $user->name . ", that user does not exist.";
    }
}
else {
    echo $user->name . ", you forgot to say who to shout out.";
}