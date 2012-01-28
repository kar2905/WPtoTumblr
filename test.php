<?php
$post=array();
$xml=simplexml_load_file("xml.xml","SimpleXMLElement",LIBXML_NOCDATA );
$result["title"]   = $xml->xpath("/rss/channel/item/title");
$result["content"] = $xml->xpath("/rss/channel/item/content:encoded/text()");
$result["date"]=$xml->xpath("/rss/channel/item/pubDate");

foreach($result as $key=>$value)
{
	foreach($value as $k=>$val)
		$post[$k][$key]=(string)$val;
}

print_r($post);

?>
