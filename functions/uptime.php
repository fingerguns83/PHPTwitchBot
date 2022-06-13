<?php
include './secret.php';
use NewTwitchApi\HelixGuzzleClient;
use NewTwitchApi\NewTwitchApi;

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
if (isset($responseContent['data'][0])){
  $start = strtotime($responseContent['data'][0]['started_at']);
  $duration = time() - $start;
  $mins = $duration / 60;
  $hours = floor($mins / 60);
  $mins = $mins % 60;
  if ($hours < 1){
    $hourstr = "";
  }
  elseif ($hours == 1){
    $hourstr = "1 hour";
  }
  else {
    $hourstr = $hours . " hours";
  }
  if ($mins < 1){
    if ($hours < 1){
      $minstr = "less than a minute";
    }
    else {
      $minstr = "";
    }
  }
  elseif ($mins == 1){
    if ($hourstr){
      $minstr = " and 1 minute";
    }
    else {
      $minstr = "1 minute";
    }
  }
  else {
    if ($hourstr){
      $minstr = " and $mins minutes"; 
    }
    else {
      $minstr = $mins . " minutes";
    }
  }

  echo "Stream has been live for " . $hourstr . $minstr;
}