<?php
require_once('TwitterAPIExchange.php');
 
/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "123852287-m0hVH78utUjwOr8kmKC5YDkL6BKHWLbRR06rBcrs",
    'oauth_access_token_secret' => "t0F2fqAriEtbPfeRUXg1t53e1Fq7DKE4VLqyzcbhxGsAX",
    'consumer_key' => "oQbcdM7A46ZDFO6Dt3Llv14Rk",
    'consumer_secret' => "Wh19jEB1vbfVd1NAcsexs0Zl4WatW5UlrTr9KaXEVnWjGQDep6"
);
 
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
$requestMethod = "GET";
$getfield = '?screen_name=iagdotme&count=20';

$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);

if($string["errors"][0]["message"] != "") {echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$string[errors][0]["message"]."</em></p>";exit();}
echo "<pre>";
print_r($string);
echo "</pre>";
?>
