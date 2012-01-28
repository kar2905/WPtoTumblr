<?php
//Taken from FireEagle example at php.net http://www.php.net/manual/en/oauth.examples.fireeagle.php

$conskey=""; //Consumer Key
$conssec=""; //Consumer Secret
require("config.php");
$req_url = 'http://www.tumblr.com/oauth/request_token';
$authurl = 'http://www.tumblr.com/oauth/authorize';
$acc_url = 'http://www.tumblr.com/oauth/access_token';
$api_url = 'http://api.tumblr.com/v2/blog';


$blogurl="www.mkartik.net";		//Blog URL
session_start();

// In state=1 the next request should include an oauth_token.
// If it doesn't go back to 0
if(!isset($_GET['oauth_token']) && $_SESSION['state']==1) $_SESSION['state'] = 0;
try {
  $oauth = new OAuth($conskey,$conssec);
  $oauth->enableDebug();
  if(!isset($_GET['oauth_token']) && !$_SESSION['state']) {
    $request_token_info = $oauth->getRequestToken($req_url);
    $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
    $_SESSION['state'] = 1;
    header('Location: '.$authurl.'?oauth_token='.$request_token_info['oauth_token']);
    exit;
  } else if($_SESSION['state']==1) {
    $oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
    $access_token_info = $oauth->getAccessToken($acc_url);
    $_SESSION['state'] = 2;
    $_SESSION['token'] = $access_token_info['oauth_token'];
    $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
  } 
  $oauth->setToken($_SESSION['token'],$_SESSION['secret']);
  $xml=simplexml_load_file("xml.xml","SimpleXMLElement",LIBXML_NOCDATA );
$result["title"]   = $xml->xpath("/rss/channel/item/title");
$result["content"] = $xml->xpath("/rss/channel/item/content:encoded/text()");
$result["date"]=$xml->xpath("/rss/channel/item/pubDate");

foreach($result as $key=>$value)
{
	foreach($value as $k=>$val)
		$posts[$k][$key]=(string)$val;
}
foreach($posts as $post)
{
  $oauth->fetch("$api_url/$blogurl/post",array("type"=>"text","title"=>$post['title'],"body"=>$post['content'],"date"=>$post['date']),"POST");
  $json = json_decode($oauth->getLastResponse());
  print_r($json);
}
} catch(OAuthException $E) {
  print_r($E);
}
?>
